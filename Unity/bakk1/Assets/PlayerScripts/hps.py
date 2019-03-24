import time

rec_time = 10

arr = []
start = None
last = None

while True:
	raw_input("")
	t = time.time()
	if not start:
		start = t
	if last:
		arr.append(t - last)
	else:
		last = t
	print time.time() - start
	last = t
	if time.time() - start > rec_time:
		break

print "Hits/second:", 1/(sum(arr)/len(arr))
