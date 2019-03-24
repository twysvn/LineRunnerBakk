
import numpy as np
import math
import matplotlib.pyplot as plt



def main():
    y_data = [0.65,0.54,0.53,0.52,0.4,0.38,0.28,0.17,0.29,0.46,0.55,0.63,0.69,0.76,0.81]
    x_data = [5.0, 6.0, 7.0, 8.0, 9.0, 10.0, 11.0, 12.0, 13.0, 14.0, 15.0, 16.0, 17.0, 18.0, 19.0]

    last = 0
    for pot in range(2, 10):
        print("Pot = "+str(pot))
        for k in range(1, len(y_data) - 1):
            y = [y_data[int(i)] for i in np.linspace(0,len(y_data)-1,k)]
            x = [x_data[int(i)] for i in np.linspace(0,len(x_data)-1,k)]

            o_y = y
            o_x = x

            # pot = 4

            phi = np.matrix([[math.pow(x[i], p) for i in range(0, k)] for p in range(0, pot + 1)]).transpose()
            y = np.matrix(y).transpose()

            # theta = np.linalg.pinv(phi.transpose()*phi)*phi.transpose()*y
            theta = np.linalg.pinv(phi)*y

            xp = np.matrix(np.linspace(min(x_data), max(x_data), 100))
            yp = []
            for p in range(0, pot + 1):
                yp += [(np.power(np.asarray(xp)[0], p)) * theta.item(p, 0)]
            yp = np.matrix(yp).sum(axis=0)

            # yp = theta.item(0, 0) + theta.item(1, 0) * xp + theta.item(2, 0) * np.power(xp, 2) + theta.item(3, 0) * np.power(xp, 3) + theta.item(4, 0) * np.power(xp, 4)

            diff = 0
            for i in range(0, len(y_data)):
                val = sum([float(theta.item(p, 0) * np.power(x_data[i], p)) for p in range(0, pot + 1)])
                if(y_data[i] not in o_y):
                    diff += abs(y_data[i] - val)
                # diff += abs(y_data[i] - val)

            # if (abs(last-diff) < 0.1):
            if (diff < 0.5):
                print(k, diff, last-diff, "<----", sep="\t")
            else:
                print(k, diff, abs(last-diff), sep="\t")

            if (pot == 6 and k == 8):
                print(theta)
                plt.plot(np.asarray(xp)[0],np.asarray(yp)[0])
                plt.plot(x_data,y_data)
                plt.ylabel('difficulty')
                plt.xlabel('speed')
                plt.title('pot='+str(pot)+' testset='+str(k)+' error='+str(diff))
                plt.show()
            last = diff

if __name__ == '__main__':
    main()
