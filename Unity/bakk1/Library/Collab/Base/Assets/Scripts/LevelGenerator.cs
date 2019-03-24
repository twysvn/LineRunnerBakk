using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using System;
using System.Linq;
using UnityEngine.UI;


public enum BlockType {Flat, Hole, obstacle};

public class LevelGenerator : MonoBehaviour {

	public bool every_second_flat;
	public bool max_one_hole = true;
	public bool all_flat = false;

	[HideInInspector] public int chunk_length = 40;
	public int block_length = 7;

	public int obstacle_height = 3;

	public double p_flat  = 0.2f;
	public double p_hole  = 0.4f;
	public double p_obstacle = 0.4f;

	private int chunk_offset = 0;

	public GameObject sprite_prefab;

	private int start_y = 0;
	private int start_x = 0;

	public float sprite_width = 0;
	public float sprite_height = 0;

	private BlockType last_type = BlockType.Flat;

	System.Random rand = new System.Random(DateTime.Now.Millisecond);
	private BlockType[] randon = new BlockType[100];

	[HideInInspector] public List<BlockType> blocks = new List<BlockType>();

	private uint seed = 123456789;
	private List<uint> chunk_seeds = new List<uint> ();

	// Use this for initialization
	void Start () {
		chunk_length = 40;
	}

	public void init(){
		calculate_sprite_sizes();
		chunk_offset = 0;
		blocks.Clear ();
		last_type = BlockType.Flat;

		calculate_randon ();
		generate ();
	}

	public void calculate_sprite_sizes() {
		var p1 = sprite_prefab.transform.TransformPoint (0, 0, 0);
		var p2 = sprite_prefab.transform.TransformPoint (1, 1, 0);
		sprite_width = p2.x - p1.x;
		sprite_height = p2.y - p1.y;
	}

	public uint randomize_params(double speed, double force, double gravity) {
		if(sprite_width == 0)
			calculate_sprite_sizes();

		var angle = Mathf.Atan2((float)speed, (float)force);
		var v0 = Mathf.Sqrt ((float)(speed * speed + force * force));
		var maxDist = ((v0 * v0) * (Mathf.Sin ((angle) * 2))) / (gravity * 9.81);

		var maxHeight = (force*force)/(2*gravity*9.81);
		var tmp = (seed = (uint)rand.Next (10, 123456789));

		var max_block_length = 40.0;
		block_length = (int) rand.Next (2,
								(int)Math.Max(
									Math.Min(maxDist, max_block_length),
									2.0
								)
							 );

		obstacle_height = (int) rand.Next (1, 
								(int)Math.Max(
									Math.Min(maxHeight, 10.0),
									1.0
								)
							 );

		p_flat  = Math.Round(randomDouble (0, 0.8), 1);
		p_hole  = Math.Round(randomDouble (0, 1 - p_flat), 1);
		p_obstacle = Math.Round(1 - p_flat - p_hole, 1);

		Debug.Log ("flat: "+p_flat + "\t " +"hole: "+ p_hole + "\t " +"obstacle: "+ p_obstacle);
		return tmp;
	}

	public void set_params(double p_flat, double p_hole, double p_obstacle, int block_lenght, int obstacle_height, uint seed) {
		this.p_flat = p_flat;
		this.p_hole = p_hole;
		this.p_obstacle = p_obstacle;
		this.block_length = block_lenght;
		this.obstacle_height = obstacle_height;
		this.seed = seed;
		Debug.Log("set_params "+this.seed+" "+seed);
	}

	// Update is called once per frame
	void Update () {
		GameObject[] players = GameObject.FindGameObjectsWithTag ("Player");
		if (players.Length <= 0) {
			return;
		}
		GameObject player = players[0];

		float player_pos = player.transform.position.x;

		float chunk_pos = start_x + chunk_offset * chunk_length * block_length * sprite_width;
		float gen_new_point = chunk_pos - chunk_length * block_length * sprite_width / 2;

		if(player_pos > gen_new_point){
			generate ();
		}
	}


	void calculate_randon(){
		int offset = 0;
		int n_offset = offset;
		for (int i = offset; i < offset + (int)(p_flat*randon.Length); i++) {
			randon [i] = BlockType.Flat;
			n_offset++;
		}
		offset = n_offset;
		for (int i = offset; i < offset + (int)(p_hole*randon.Length); i++) {
			randon [i] = BlockType.Hole;
			n_offset++;
		}
		offset = n_offset;
		for (int i = offset; i < randon.Length; i++) {
			randon [i] = BlockType.obstacle;
		}
	}

	void generate() {
		chunk_seeds.Add (seed);
		for (int i = 0; i < chunk_length; i++) {
			BlockType type = BlockType.Flat;

			if (chunk_offset == 0 && i < 4) {
				type = BlockType.Flat;
			} else {
				int j = 0;
				do {
					type = get_random_type ();
					if(j++ > chunk_length){
						type = BlockType.Flat;
						break;
					}
				} while(max_one_hole && last_type == BlockType.Hole && type == BlockType.Hole);

				if (every_second_flat && i % 2 == 0) {
					type = BlockType.Flat;
				}
			}

			last_type = type;
			blocks.Add (type);

			float chunk_pos = start_x + chunk_offset * chunk_length * block_length * sprite_width;
			Debug.DrawRay (new Vector3 (chunk_pos, 1, 0), new Vector3 (0, 40f, 0), Color.cyan, 10f);
			float block_pos = chunk_pos + block_length * i * sprite_width;

			switch (type) {
			case BlockType.Flat:
				generate_flat (block_pos);
				break;
			case BlockType.Hole:
				generate_hole (block_pos);
				break;
			case BlockType.obstacle:
				generate_obstacle (block_pos);
				break;
			}
		}

		chunk_offset++;
	}


	BlockType get_random_type() {
		int r = random(randon.Length);
		if(all_flat)
			return BlockType.Flat;
		return (BlockType)randon.GetValue(r);
	}

	//https://en.wikipedia.org/wiki/Lehmer_random_number_generator
	int random(int max) {
		return (int) (seed = ((uint)(seed * 48271)) % 0x7fffffff) % max;
	}

	int random(int min, int max) {
		return random (max - min) + min;
	}

	double random(double min, double max) {
		var r = (double) random (0x7fffffff);
		return min + (min - max) * r / 0x7fffffff;
	}

	void generate_flat(float block_pos) {
		for(int i = 0; i < block_length; i++) {
			float x_pos = block_pos + i * sprite_width;

			GameObject new_sprite = (GameObject)Instantiate (sprite_prefab);
			new_sprite.tag = "Sprite";
			new_sprite.transform.position = new Vector2(x_pos, start_y);

			if (i == 0) {
				GameObject ns = (GameObject)Instantiate (sprite_prefab);
				ns.tag = "Sprite";
				ns.transform.position = new Vector2 (x_pos, start_y - sprite_height);

				float overlap = sprite_width/100f;
				BoxCollider2D collider = new_sprite.GetComponent<BoxCollider2D> ();
				collider.size = new Vector2 (block_length*sprite_width+overlap*2, sprite_height);
				collider.offset = new Vector2 (block_length*sprite_width/2 - sprite_width/2 - overlap, 0);

			} else {
				Destroy(new_sprite.GetComponent<BoxCollider2D>());
			}

		}
	}

	void generate_hole(float block_pos) { }

	void generate_obstacle(float block_pos) {
		generate_flat (block_pos);

		float obstacle_pos = block_pos + block_length / 2;
		for(int i = 1; i <= obstacle_height; i++){
			GameObject new_sprite = (GameObject)Instantiate (sprite_prefab);
			new_sprite.tag = "Sprite";

			float y_pos = start_y + i * sprite_height;
			new_sprite.transform.position = new Vector2(obstacle_pos, y_pos);
		}
	}

	public int get_block_index(Player player) {
		float player_pos = player.transform.position.x - start_x;
		float block_size = block_length * sprite_width;
		return (int)(player_pos / block_size);
	}

	public float get_next_block_pos(Player player) {
		float block_size = block_length * sprite_width;
		return (float)(get_block_index(player) + 1) * block_size;
	}

	public float get_block_pos(Player player, int block) {
		float block_size = block_length * sprite_width;
		return (float)(block) * block_size;
	}

	public float get_current_block_pos(Player player) {
		float block_size = block_length * sprite_width;
		return (float)(get_block_index(player)) * block_size;
	}

	public BlockType get_current_block(Player player) {
		return blocks [get_block_index (player)];
	}

	public BlockType get_next_block(Player player) {
		return blocks [get_block_index (player) + 1];
	}

	double randomDouble(double minimum, double maximum)
	{ 
		return rand.NextDouble() * (maximum - minimum) + minimum;
	}
}
