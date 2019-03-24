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
	private bool should_duck = false;

	bool simulate_player = true;

	private LevelGenerator levelgen = null;
	private Simulation simulation = null;

	private float topAngle;
	private float sideAngle;

	[SerializeField] public float died_at = 0;

	private double gravity = 9.81f;

	private float angle = 0;
	private double maxDist = 0;
	private int future = 1;
	private float last_center = -1;

	public bool show_rays = true;

	// Use this for initialization
	void Start () {
		rb = GetComponent<Rigidbody2D>();

		float r = GetComponent<CircleCollider2D> ().radius;
		Vector2 size = new Vector2(r, r);
		size = Vector2.Scale (size, (Vector2)transform.localScale);
		topAngle = Mathf.Atan (size.x / size.y) * Mathf.Rad2Deg;
		sideAngle = 90.0f - topAngle + 5f;

		levelgen = (LevelGenerator)GameObject.Find ("LevelGenerator").GetComponent<LevelGenerator> ();
		simulation = (Simulation)GameObject.Find ("Simulation").GetComponent<Simulation> ();
		future = Mathf.Max((int)(35/levelgen.block_length), 2);

		while (future >= levelgen.chunk_length/2) {
			levelgen.chunk_length++;
		}

		//show_rays = Debug.isDebugBuild;
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

		sigma = speed * 0.3f;

		angle = Mathf.Atan2((float)move_speed, (float)jump_force);
		var v0 = Mathf.Sqrt ((float)(move_speed * move_speed + jump_force * jump_force));
		maxDist = ((v0 * v0) * (Mathf.Sin ((angle) * 2))) / gravity;
//		maxDist = (2 * move_speed * jump_force)/gravity;
	}

	public Transform groundCheck;
	private bool grounded;

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
//				next_jump_pos = -1;
			}
			if(simulate_player)
				calculate_next_pos ();

			if ((Input.GetKeyDown (KeyCode.Space)) && grounded) {
				// jumping
				jump ();
			} else if (Input.GetKeyDown (KeyCode.RightShift) && grounded) {
				// ducking?
				duck ();
			} else if (Input.GetKeyDown (KeyCode.Backspace)) {
				removeMe ();
			}

			// if(simulate_player)
			// 	simulate ();
		}
			
	}

	void FixedUpdate() {
		
		if (should_jump) {
			// jumping
			rb.velocity = new Vector2 (rb.velocity.x, 0);
			rb.AddForce (Vector2.up * (float)jump_force, ForceMode2D.Impulse);
			should_jump = false;
		} else if(should_duck) {
			// ducking
			rb.AddForce (Vector2.down * (float)jump_force, ForceMode2D.Impulse);
			should_duck = false;
		}

		if (transform.position.x > simulation.end_position) {
//			rb.velocity = new Vector2(0, 0);
			removeMe ();
		}
	}

	public void jump(){
		should_jump = true;
	}
		
	public void duck() {
		should_duck = true;
	}

	void OnCollisionEnter2D (Collision2D col)
	{
		
//		is_jumping = true;
		if (col.gameObject.tag == "Sprite") {

//			rb.velocity = new Vector2 (rb.velocity.x, 0);

			if (col.contacts.Length <= 0) {
				return;
			}
			Vector3 v = (Vector3)col.contacts[0].point - transform.position;
			if (Vector3.Angle(v, transform.up) <= topAngle) {
//				Debug.Log("Collision on top");
			}
			else if (Vector3.Angle(v, transform.right) <= sideAngle)  {
//				Debug.Log("Collision on left");
				removeMe();
			}
			else if (Vector3.Angle(v, -transform.right) <= sideAngle) {
//				Debug.Log ("Collision on right");
//				removeMe();
			}
			else {
//				Debug.Log("Collision on bottom");
//				is_jumping = false;
//				next_jump_pos = -1;
//				calculate_next_pos ();
			}
//			next_jump_pos = -1;

		} else {
			Physics2D.IgnoreCollision(col.gameObject.GetComponent<Collider2D>(), GetComponent<Collider2D>());
		}
	}

	private void calculate_next_pos() {
		int current_block_index = levelgen.get_block_index (this);

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

				//TODO if pos behind player
//				if (pos < transform.position.x) {
//					pos = transform.position.x + sigma / 2;
//				}

				float ran = UnityEngine.Random.Range ((float)-sigma / 2, (float)sigma / 2);
				pos += ran;

				var obstacle_center = obstacle_pos + levelgen.block_length * levelgen.sprite_width / 2f;

//				Debug.Log (pos + " >= " +  transform.position.x + " " +  (pos >= transform.position.x));
				if (transform.position.x > obstacle_center) {
					break;
				}
//				if ((pos >= transform.position.x || grounded)) {
					if (!(pos > next_jump_pos - sigma && pos < next_jump_pos + sigma) /*&& grounded*/) {
						next_jump_pos = pos;
						last_center = obstacle_center;


						if (show_rays) {
							Debug.DrawRay (new Vector3 (next_jump_pos, 1, 0), new Vector3 (0, 10, 0), Color.blue, 2f);
//							Debug.DrawRay (new Vector3 ((float)(next_jump_pos + maxDist), 1f, 0f), new Vector3 (0.0f, 10f, 0f), Color.white, 2f);
						}
						break;
					} else if (next_jump_pos > 0) {
						break;
					}

//				} else if (transform.position.x < obstacle_center) {
//					break;
//				} else {
//					Debug.DrawRay (new Vector3 (next_jump_pos, 1, 0), new Vector3 (0, 10, 0), Color.yellow, 2f);
//				}
			} else {
			}
		}
			
		// jump
		var player = transform.position.x;
		var block_center = levelgen.get_block_pos(this, levelgen.get_block_index (this)) + levelgen.block_length * levelgen.sprite_width / 2f;

		if(last_center > 0 && player > last_center){
			if (show_rays)
				Debug.DrawRay (new Vector3 (next_jump_pos, 1, 0), new Vector3 (0, 10, 0), Color.gray, 1f);
			next_jump_pos = -1;
			last_center = -1;
		}

		if (grounded && next_jump_pos > 0 /*&& levelgen.get_current_block(this) != BlockType.Flat */ &&
		    (player >= next_jump_pos || (next_jump_pos < player && player < block_center))) {

//			Debug.Log ("jump " + levelgen.get_current_block (this) + " " + next_jump_pos + " " + transform.position.x + " " + (player >= next_jump_pos) + " or " + (next_jump_pos < player && player < block_center));
			if (show_rays)
				Debug.DrawRay (new Vector3 (next_jump_pos, 1, 0), new Vector3 (0, 10, 0), Color.green, 1f);
			
			jump ();
			next_jump_pos = -1;
		} else {
		}
	}

	private float calculate_block_jump_pos(int index) {
		double pos = levelgen.get_block_pos (this, index);
//		Debug.DrawRay (new Vector3 (pos, 1, 0), new Vector3 (0, 10, 0), Color.white, 4f);
		return (float) (pos - maxDist / 2.0 + levelgen.block_length * levelgen.sprite_width / 2.0);
	}

	private void simulate() {
//		BlockType current_block_type = levelgen.get_current_block (this);
//		BlockType next_block_type = levelgen.get_next_block (this);
//		float player_pos = transform.position.x;
//
//		float block_pos = 0;
//
//
//		if (!is_obstacle && (current_block_type == BlockType.Boner || current_block_type == BlockType.Hole)) {
//			block_pos = levelgen.get_current_block_pos (this);
//			is_obstacle = true;
//		}
//
//		if (player_pos > levelgen.get_current_block_pos (this) + levelgen.block_length * levelgen.sprite_width) {
//			is_obstacle = false;
//			next_jump_pos = -1;
//		}
//
//		if (!is_obstacle && (next_block_type == BlockType.Boner || next_block_type == BlockType.Hole)) {
//			block_pos = levelgen.get_next_block_pos (this);
//			is_obstacle = true;
//		}
//
//		if (is_obstacle) {
//			if (next_jump_pos < 0) {
//
//				//next_jump_pos = block_pos - levelgen.sprite_width * 2;
//				next_jump_pos = block_pos - maxDist * 0.96f / 2.0f + levelgen.block_length * levelgen.sprite_width / 2f;
//
//				float ran = UnityEngine.Random.Range (-sigma / 2, sigma / 2);
//
//				//TODO check if this is ok
//				if (next_jump_pos < player_pos) {
//					next_jump_pos = player_pos + sigma / 2;
//				}
//				next_jump_pos += ran;
//
//				Debug.DrawRay (new Vector3(next_jump_pos, 1, 0), new Vector3(0, 10, 0), Color.red, 4.0f);
//			}
//			float dp = 1.1f;
//			if (player_pos >= next_jump_pos && transform.position.y < dp && levelgen.get_current_block(this) != BlockType.Hole) {
//
//				//				GameObject new_sprite = (GameObject)Instantiate (levelgen.sprite_prefab);
//				//				var x = transform.position.x;
//				//				var z = new_sprite.transform.position.z;
//				//				new_sprite.transform.position = new Vector3 (x + maxDist * 0.96f, 10, z);
//
//				jump ();
//				next_jump_pos = -1;
//				is_obstacle = false;
//			}
//		} else {
//			next_jump_pos = -1;
//		}
	}

//	void OnCollisionExit2D (Collision2D col) {
//		Debug.Log (this.transform.position.y);
//		if (col.gameObject.tag == "Sprite" && this.transform.position.y < 0) {
//			is_jumping = true;
//		}
//	}

	void removeMe() {
		this.tag = "Dead";
		died_at = transform.position.x;
		//Destroy (this);
		//gameObject.SetActive(false);
		alive = false;

		// todo: finish game
	}

}