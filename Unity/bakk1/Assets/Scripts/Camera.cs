using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class Camera : MonoBehaviour {

	private Rigidbody2D rb;
	public int move_speed;

	private Vector3 start_pos;

	private int counter = 0;

	// Use this for initialization
	void Start () {
//		rb = GetComponent<Rigidbody2D>();
//		Vector3 movement = new Vector2 (/*moveHorizontal **/ move_speed, rb.velocity.y);
//		rb.velocity = movement;
	}

	void Awake() {
		start_pos = gameObject.transform.position;
	}

	// Update is called once per frame
	void LateUpdate () {
		
//		Vector3 movement = new Vector2 (/*moveHorizontal **/ move_speed, rb.velocity.y);
//		rb.velocity = movement;

		GameObject[] players = GameObject.FindGameObjectsWithTag ("Player");
		float max_vel = 0;
		float max_x = 0;
		if (players.Length > 0) {
			Rigidbody2D r = players [0].GetComponent<Rigidbody2D>();
//			max_vel = max_vel > r.velocity.x ? max_vel : r.velocity.x;
			max_x = max_x > r.transform.position.x ? max_x : r.transform.position.x;
		}
//		if (counter >= 30) {
//			rb.velocity = new Vector2(max_vel, 0);
//			transform.position = new Vector3(max_x, transform.position.y, transform.position.z);
//			counter = 0;
//		}

		if (players.Length > 0) {
//			var x = players [0].transform.position.x;
			transform.position = new Vector3(max_x , transform.position.y ,transform.position.z );
		}
//		counter++;
	}

	public void reset() {
		gameObject.transform.position = start_pos;
	}

}

