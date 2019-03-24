using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;
using System;
using System.Runtime.InteropServices;

public class Simulation : MonoBehaviour {

	public GameObject player;

	public Text label_number_of_players;
	public Text label_score;
	public Text label_actual_score;

	public bool is_enabled;
	private bool should_randomize = true;

	public double end_position = 500;

	public Text p_flat;
	public Text p_hole;
	public Text p_boner;
	public Text boner_height;
	public Text speed;
	public Text force;
	public Text gravity;
	public Text block_length;
	public Text seed_label;

	private System.Random random = new System.Random(DateTime.Now.Millisecond);
	public int number_players = 1;

	public float start_x;
	public float start_y;

	private bool fin_d = false;

	private LevelGenerator lg;

	private float original_time;

	private int update_counter = 0;

	[DllImport("__Internal")]
	private static extern void SetParameters (double p_flat, double p_hole, double p_obstacle, int obst_height, 
		double speed, double force, double gravity, int block_length, uint seed, int score, double score_scaled, 
		int players, int max_players);

	[DllImport("__Internal")]
	private static extern void SetScore (int score, double score_scaled, double standard_deviation, int players, 
		int max_players);

	[DllImport("__Internal")]
	private static extern void SetChart (int[] data, int size);

	[DllImport("__Internal")]
	private static extern void SaveParameters ();

    [DllImport("__Internal")]
	private static extern void SetTime (double time);

	private double s_p_flat;
	private double s_p_hole;
	private double s_p_boner;
	private int s_obst_height;
	private double s_speed;
	private double s_force;
	private double s_gravity;
	private int s_block_len;
	private uint s_seed;
	private int s_players;

	// Use this for initialization
	void Start () {
		Application.targetFrameRate = 30;
		original_time = Time.timeScale;
		start_game ();
	}

	void Awake() {
		#if !UNITY_EDITOR && UNITY_WEBGL

			WebGLInput.captureAllKeyboardInput = false;

			Destroy(label_number_of_players);
			Destroy(label_score);
			Destroy(label_actual_score);

			Destroy(p_flat);
			Destroy(p_hole);
			Destroy(p_boner);
			Destroy(boner_height);
			Destroy(speed);
			Destroy(force);
			Destroy(gravity);
			Destroy(block_length);
			Destroy(seed_label);

		#endif
	}

	void start_game (){
		Time.timeScale   = original_time;
		double speed_v   = 0;
		double force_v   = 0;
		double gravity_v = 0;

		#if !UNITY_EDITOR && UNITY_WEBGL
			SetTime(1.0);
		#endif

		if (is_enabled && should_randomize) {
			speed_v   = Math.Round(randomDouble (5,   20),  2);
			s_speed   = speed_v;
			force_v   = Math.Round(randomDouble (4,   20),  2);
			s_force   = force_v;
			gravity_v = Math.Round(randomDouble (0.6, 3.6), 2);
			s_gravity = gravity_v;
		}
		if (!should_randomize) {
			speed_v   = s_speed;
			force_v   = s_force;
			gravity_v = s_gravity;
		}

		for (int i = 0; i < number_players; i++) {
			GameObject ns = (GameObject)Instantiate (player);
			Player p = ns.GetComponent<Player> ();
			p.set_simulation_enabled(false);
			if (is_enabled) {
				p.set_params (speed_v, force_v, gravity_v);
				p.set_simulation_enabled(true);
			}
			ns.transform.position = new Vector3(start_x, start_y, 0);
		}
		GameObject[] sprites = GameObject.FindGameObjectsWithTag ("Sprite");
		for (int i = 0; i < sprites.Length; i++) {
			Destroy (sprites[i]);
		}

		lg = (LevelGenerator)GameObject.Find ("LevelGenerator").GetComponent<LevelGenerator> ();

		if (is_enabled && should_randomize){
			
			s_seed 		  = lg.randomize_params (speed_v, force_v, gravity_v);
			s_p_flat 	  = lg.p_flat;
			s_p_hole   	  = lg.p_hole;
			s_p_boner 	  = lg.p_boner;
			s_block_len   = lg.block_length;

			s_obst_height = lg.boner_height;
		}
		if (!should_randomize)
			lg.set_params (s_p_flat, s_p_hole, s_p_boner, s_block_len, s_obst_height, s_seed);

		lg.init ();

		#if !UNITY_EDITOR && UNITY_WEBGL
			SetParameters (lg.p_flat, lg.p_hole, lg.p_boner, lg.boner_height, speed_v, force_v, gravity_v, lg.block_length, s_seed, 0, 0, 0, number_players);
		#else
			p_flat.text 		= "P(flat) :    \t\t\t" + (lg.p_flat).ToString();
			p_hole.text 		= "P(hole) :    \t\t" + (lg.p_hole).ToString();
			p_boner.text 		= "P(obstacle) :\t" + (lg.p_boner).ToString();
			boner_height.text 	= "obstacle_height:\t" + (lg.boner_height).ToString();
			speed.text 			= "speed:\t\t\t\t" + (speed_v).ToString();
			force.text 			= "force:\t\t\t\t" + (force_v).ToString();
			gravity.text 		= "gravity:\t\t\t\t" + (gravity_v).ToString();
			block_length.text 	= "block_length:\t" + (lg.block_length).ToString();
			seed_label.text 	= "seed:\t" + (s_seed).ToString("#########");

		#endif

		GameObject.Find ("Camera").GetComponent<Camera> ().reset ();
		fin_d = false;
	}

	void restart() {
		GameObject[] player = GameObject.FindGameObjectsWithTag ("Player");
		for (int i = 0; i < player.Length; i++) {
			Destroy (player[i]);
		}
	}

	void remove_dead() {
		GameObject[] player = GameObject.FindGameObjectsWithTag ("Dead");
		for (int i = 0; i < player.Length; i++) {
			Destroy (player[i]);
		}
	}

	double calculate_difficulty() {
		GameObject[] player = GameObject.FindGameObjectsWithTag ("Dead");
		double difficulty = 0;
		for (int i = 0; i < player.Length; i++) {
			difficulty += player[i].GetComponent<Player>().died_at;
		}
		if (player.Length == 0)
			return 1;
		difficulty /= player.Length;
//		Debug.Log ("Difficulty: " + difficulty);
		return Math.Round(difficulty / end_position, 2);
	}

	double[] calculate_distances() {
		GameObject[] player = GameObject.FindGameObjectsWithTag ("Dead");
		double[] dist = new double[player.Length];
		for (int i = 0; i < player.Length; i++) {
			dist[i] = player[i].GetComponent<Player>().died_at / end_position;
		}
		return dist;
	}
		
	int[] calculate_survial_diagram() {
		int accuracy = 10;
		int points = (int)end_position / accuracy;
		int[] ret = new int[points];

		for (int i = 0; i < ret.Length; i++) {
			ret[i] = 0;
		}

		GameObject[] player = GameObject.FindGameObjectsWithTag ("Dead");
		for (int i = 0; i < player.Length; i++) {

			float died_at = player [i].GetComponent<Player> ().died_at;

			for (int j = 0; j < died_at / accuracy; j++) {
				ret[j]++;
			}
		}

		player = GameObject.FindGameObjectsWithTag ("Player");
		double pos = end_position;
		if (player.Length > 0) {
			pos = player [0].transform.position.x;
		}


		//TODO only to current
		for (int j = 0; j < ((int) pos / accuracy) && j < ret.Length; j++) {
			ret[j] += player.Length;
		}
		return ret;
	}

	double calculate_variance() {
		double m = calculate_difficulty();
		double v = 0;
		double[] distances = calculate_distances ();
		foreach (double d in distances) {
			v += Math.Pow(d - m, 2.0);
		}
		if (distances.Length <= 1)
			return 1.0;
		return Math.Round(v/distances.Length, 2);
	}

	double calculate_standard_deviation() {
		return Math.Round(Math.Sqrt (calculate_variance ()), 2);
	}

	// Update is called once per frame
	void Update () {
		GameObject[] player = GameObject.FindGameObjectsWithTag ("Player");
		#if UNITY_EDITOR || !UNITY_WEBGL
			label_number_of_players.text = (player.Length).ToString () + "/" + number_players.ToString();
		#endif

		if (player.Length <= 0 && !fin_d) {
			fin_d = true;
			#if !UNITY_EDITOR && UNITY_WEBGL
				calc_score_labels();	// update score one more last time
				SaveParameters ();
			#endif
			
			start_game ();
			remove_dead ();
		}

		if (Input.GetKeyDown(KeyCode.DownArrow)) {
			slower();
		} else if (Input.GetKeyDown(KeyCode.UpArrow)) {
			faster();
		} else if (Input.GetKeyDown(KeyCode.Return)) {
			resetTime();
		}
	}

	void faster() {
		Time.timeScale = Time.timeScale * 2f;
		#if !UNITY_EDITOR && UNITY_WEBGL
			SetTime(Time.timeScale / original_time);
		#endif
	}

	void slower() {
		Time.timeScale = Time.timeScale * 0.5f;
		#if !UNITY_EDITOR && UNITY_WEBGL
			SetTime(Time.timeScale / original_time);
		#endif
	}

	void resetTime() {
		Time.timeScale = original_time;
		#if !UNITY_EDITOR && UNITY_WEBGL
			SetTime(Time.timeScale / original_time);
		#endif
	}

	void pause() {
		Time.timeScale = 0;
		#if !UNITY_EDITOR && UNITY_WEBGL
			SetTime(0.0);
		#endif
	}

	void FixedUpdate() {
		calc_score_labels();
	}

	void calc_score_labels() {
		int score = 0;
		GameObject[] players = GameObject.FindGameObjectsWithTag ("Player");
		foreach(GameObject fooObj in players)
		{
			Player p;
			if (fooObj != null && (p = fooObj.GetComponent<Player> ()) != null) {
				score += (int)p.transform.position.x;
			}
		}
		var len = players.Length == 0 ? 1 : players.Length;
		#if UNITY_EDITOR || !UNITY_WEBGL
			label_score.text = String.Format ("{0}", (score / len));
		#endif
		var df = calculate_difficulty ();
		var sd = calculate_standard_deviation ();
		#if UNITY_EDITOR || !UNITY_WEBGL
			label_actual_score.text = df + " (" + sd + ")";
		#endif

		#if !UNITY_EDITOR && UNITY_WEBGL
			SetScore (score / len, df, sd, players.Length, number_players);
			
			if (update_counter == 1 || players.Length <= 0){
				int[] arr = calculate_survial_diagram ();
				SetChart(arr, arr.Length);
			}
		#endif
		update_counter++;
		if (update_counter >= 100) {
			update_counter = 0;
		}
	}

	void setParams(string s){

		should_randomize = false;
		string[] vars 	= s.Split (';');
		s_p_flat 		= double.Parse(vars [0]);
		s_p_hole 		= double.Parse(vars[1]);
		s_p_boner 		= double.Parse(vars[2]);
		s_obst_height 	= int.Parse(vars[3]);
		s_speed 		= double.Parse(vars[4]);
		s_force 		= double.Parse(vars[5]);
		s_gravity 		= double.Parse(vars[6]);
		s_block_len 	= int.Parse(vars[7]);
		s_seed 			= uint.Parse (vars[8]);
		s_players 		= int.Parse(vars[9]);
	}

	void setProbabilities(string s) {
		string[] vars = s.Split (';');
		s_p_flat  = double.Parse(vars[0]);
		s_p_hole  = double.Parse(vars[1]);
		s_p_boner = double.Parse(vars[2]);
	}

	void setSpeed(string s) {
		// should_randomize = false;
		s_speed = double.Parse (s);
	}

	void setForce(string s) {
		// should_randomize = false;
		s_force = double.Parse (s);
	}

	void setGravity(string s) {
		// should_randomize = false;
		s_gravity = double.Parse (s);
	}

	void setBlockLength(string s) {
		// should_randomize = false;
		s_block_len = int.Parse (s);
	}

	void setPlayers(string s) {
		number_players = int.Parse (s);
	}

	void setObstacleHeight(string s) {
		// should_randomize = false;
		s_obst_height = int.Parse (s);
	}

	void enableRandom() {
		should_randomize = true;
	}

	void disableRandom() {
		should_randomize = false;
	}

	double randomDouble(double minimum, double maximum)
	{ 
		return random.NextDouble() * (maximum - minimum) + minimum;
	}
		
}
