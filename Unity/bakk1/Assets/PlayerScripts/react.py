import time
import random

arr = []
top = 10
for i in range(0, top):
	raw_input("Ready?")
	time.sleep(random.randint(1, 3))
	start = time.time()
	raw_input("Hit me baby one more time")
	now = time.time()
	delta = now-start
	if delta > 0.1:
		arr.append(delta)
		print now-start
	else:
		print("\nPretty fast. Too fast.\nLets try that again!")
		top += 1

print "\nAvg reaction ", sum(arr)/len(arr)	
