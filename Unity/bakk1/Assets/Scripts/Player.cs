using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;

public class Player : MonoBehaviour {

	// settable
	public double jump_force;
	public double move_speed;

	// other
	private bool is_jumping;
	private bool facing_right;

	private Rigidbody2D rb;

	[SerializeField] public float next_jump_pos = -1;
	private double sigma = 0;

	private bool alive = true;

	[SerializeField] public bool should_jump = false;

	bool simulate_player = true;

	private LevelGenerator levelgen = null;
	private Simulation simulation = null;

	private float topAngle;
	private float sideAngle;

	[SerializeField] public float died_at = 0;

	private double gravity = 9.81f;

	private float angle = 0;
	private double maxDist = 0;
	private double maxHeight = 0;
	private int future = 1;
	private float last_center = -1;

	public bool show_rays = true;

	private float time_per_action = 1.0f/7.257f;
	private float last_action;

	// Use this for initialization
	void Start () {
		rb = GetComponent<Rigidbody2D>();

		float r = GetComponent<CircleCollider2D> ().radius;
		Vector2 size = new Vector2(r, r);
		size = Vector2.Scale (size, (Vector2)transform.localScale);
		topAngle = Mathf.Atan (size.x / size.y) * Mathf.Rad2Deg;
		sideAngle = 100.0f - topAngle;

		levelgen = (LevelGenerator)GameObject.Find ("LevelGenerator").GetComponent<LevelGenerator> ();
		simulation = (Simulation)GameObject.Find ("Simulation").GetComponent<Simulation> ();
		future = Mathf.Max((int)(35/levelgen.block_length), 2);

		while (future >= levelgen.chunk_length/2) {
			levelgen.chunk_length++;
		}
		died_at = 0;
	}

	public void set_simulation_enabled(bool en) {
		simulate_player = en;
	}

	public void set_params(double speed, double force, double grvity) {
		move_speed = speed;
		jump_force = force;
		gravity = grvity * (-Physics2D.gravity.y);
		rb = GetComponent<Rigidbody2D>();
		rb.gravityScale = (float)grvity;

		sigma = speed * 0.327f;

		angle = Mathf.Atan2((float)move_speed, (float)jump_force);
		var v0 = Mathf.Sqrt ((float)(move_speed * move_speed + jump_force * jump_force));
		maxDist = ((v0 * v0) * (Mathf.Sin ((angle) * 2))) / gravity * 0.95;
		maxHeight = (jump_force*jump_force)/(2*gravity) * 0.95;
	}

	public float get_score() {
		if (died_at > 0)
			return died_at;
		if(simulation)
			return Time.time - simulation.start_time;
		return 0;
	}

	public Transform groundCheck;
	private bool grounded;
	private bool last_grounded;

	// Update is called once per frame
	void Update () {

		if (alive) {
			// moving

			float moveHorizontal = Input.GetAxis ("Horizontal");
			float moveVertical = Input.GetAxis ("Vertical");
			Vector3 movement = new Vector2 (/*moveHorizontal **/ (float)move_speed, rb.velocity.y);
			rb.velocity = movement;

			if (this.transform.position.y < -1) {
				removeMe ();
			}

			grounded = Physics2D.Linecast(transform.position, groundCheck.position, 1 << LayerMask.NameToLayer("Ground"));
			if (grounded) {
				rb.velocity = new Vector2 (rb.velocity.x, 0);
			}
			last_grounded = grounded;
			if(simulate_player)
				calculate_next_pos ();

			if ((Input.GetKeyDown (KeyCode.Space)) && grounded) {
				jump ();
			} else if (Input.GetKeyDown (KeyCode.Backspace)) {
				removeMe ();
			}
		}
			
	}

	void FixedUpdate() {
		
		if (should_jump) {
			// jumping
			rb.velocity = new Vector2 (rb.velocity.x, 0);
			rb.AddForce (Vector2.up * (float)jump_force, ForceMode2D.Impulse);

			if (levelgen.all_flat || show_rays) {
				angle = Mathf.Atan2((float)move_speed, (float)jump_force);

				Debug.DrawRay (new Vector3 (transform.position.x + (float)maxDist, 1, 0), new Vector3 (0, (float)maxHeight, 0), Color.white, 2f);
				Debug.DrawRay (new Vector3 (transform.position.x, transform.position.y + (float)maxHeight, 0), new Vector3 ((float)maxDist, 0, 0), Color.white, 2f);
			}

			should_jump = false;
		}

		if (get_score() > simulation.end_position) {
			if(simulation.survivors == 0) simulation.survivors = GameObject.FindGameObjectsWithTag ("Player").Length;
			removeMe ();
		}
	}

	public void jump(){
		should_jump = true;
	}

	void OnCollisionEnter2D (Collision2D col)
	{
		if (col.gameObject.tag == "Sprite") {

//			rb.velocity = new Vector2 (rb.velocity.x, 0);

			if (col.contacts.Length <= 0) {
				return;
			}
			Vector3 v = (Vector3)col.contacts[0].point - transform.position;
			// if collision on left side of obstacle
			if (Vector3.Angle(v, transform.right) <= sideAngle)  {
				removeMe();
			}

		} else {
			Physics2D.IgnoreCollision(col.gameObject.GetComponent<Collider2D>(), GetComponent<Collider2D>());
		}
	}

	private void calculate_next_pos() {
		int current_block_index = levelgen.get_block_index (this);

		if(next_jump_pos <= 0)
		for (int i = current_block_index; i < current_block_index + future; i++) {

			if (i >= levelgen.blocks.Count) {
				break;
				Debug.Log (i + " vs " + levelgen.blocks.Count);
			}

			if (levelgen.blocks [i] != BlockType.Flat) {

				float pos = calculate_block_jump_pos (i);
				var obstacle_pos = levelgen.get_block_pos (this, i);

				if (levelgen.blocks [i] == BlockType.Hole && pos > obstacle_pos) {
					pos = obstacle_pos;
				}
				
				if (pos < transform.position.x && grounded) {
					pos = transform.position.x + (float)sigma / 2;
				}

				float ran = UnityEngine.Random.Range ((float)-sigma / 2, (float)sigma / 2);
				pos += ran;

				var obstacle_center = obstacle_pos + levelgen.block_length * levelgen.sprite_width / 2f;
		
				if (transform.position.x > obstacle_center) {
					break;
				}
				if (!(pos > next_jump_pos - sigma && pos < next_jump_pos + sigma)) {
					next_jump_pos = pos;
					last_center = obstacle_center;
				
					if (show_rays){
						Debug.DrawRay (new Vector3 (next_jump_pos, 1, 0), new Vector3 (0, (float)maxHeight, 0), Color.blue, 2f);
					}
						
					break;
				} else if (next_jump_pos > 0) {
					break;
				}
			}
		}
			
		// jump
		var player = transform.position.x;
		var block_center = levelgen.get_block_pos(this, levelgen.get_block_index (this)) + levelgen.block_length * levelgen.sprite_width / 2f;

		if(last_center > 0 && player > last_center){
			
			if (show_rays){
				Debug.DrawRay (new Vector3 (next_jump_pos, 1, 0), new Vector3 (0, (float)maxHeight, 0), Color.gray, 1f);
			}
				
			next_jump_pos = -1;
			last_center = -1;
		}

		if (grounded && next_jump_pos > 0 /*&& levelgen.get_current_block(this) != BlockType.Flat */ &&
		    (player >= next_jump_pos || (next_jump_pos < player && player < block_center))) {

			if (show_rays){
				Debug.DrawRay (new Vector3 (next_jump_pos, 1, 0), new Vector3 (0, (float)maxHeight, 0), Color.green, 1f);
			}

			var now = Time.time;
			if(now >= last_action + time_per_action) {
				last_action = now;
				jump ();
				next_jump_pos = -1;
			}
		} else {
		}
	}

	private float calculate_block_jump_pos(int index) {
		double pos = levelgen.get_block_pos (this, index);
//		Debug.DrawRay (new Vector3 (pos, 1, 0), new Vector3 (0, 10, 0), Color.white, 4f);
		double offset = move_speed * 0.04;
		return (float) (pos - maxDist / 2.0 + levelgen.block_length * levelgen.sprite_width / 2.0 - offset);
	}

	public void removeMe() {
		if (alive) {
			this.tag = "Dead";
			died_at = get_score ();
			alive = false;
		}
		// todo: finish game
	}

}
