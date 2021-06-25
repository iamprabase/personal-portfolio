from flask_restful import Resource
from flask import jsonify, request
import logging
import json
import numpy as np
from pykalman import KalmanFilter
import pandas as pd

logging.basicConfig(filename='debug.log',level=logging.DEBUG,filemode='w')
class Gps(Resource):
    def get(self):
        print("request:\n",request);
        jsonData = request.get_json(force = True)
        if not jsonData:
            return {'result':'no input data provided'},400
        return {'result':  jsonData},200

    def post(self):
        jsonData = request.get_json(force = True)
        # strRawData = jsonData["raw_data"]
        strRawData = '[{"datetime":"2019-02-20 17:24:09","latitude":26.4674398,"longitude":87.2838534,"altitude":100},{"datetime":"2019-02-20 17:24:24","latitude":26.4674554,"longitude":87.2837988,"altitude":100},{"datetime":"2019-02-20 17:24:33","latitude":26.4674697,"longitude":87.2837883,"altitude":100},{"datetime":"2019-02-20 17:24:48","latitude":26.4674697,"longitude":87.5557883,"altitude":100}]'
        data = json.loads(strRawData)
        logging.info(data)

        observed_lat = []
        observed_lon = []
        observed_alt = []
        obs_dt = []

        for i in range(len(data)):
          observed_lat  =   np.append(observed_lat, data[i]['latitude'])
          observed_lon  =   np.append(observed_lon, data[i]['longitude'])
          observed_alt = np.append(observed_alt, data[i]['altitude'])
          obs_dt = np.append(obs_dt, data[i]['datetime'])
          coords = pd.DataFrame({
            'lat': observed_lat,
            'lon': observed_lon,
            'ele': observed_alt,
            'time': obs_dt
          })

        logging.info(coords)

        measurements = np.ma.masked_invalid(coords[['lon', 'lat', 'ele']].values)

        # logging.info(measurements)


        # transition_matrices
        F = np.array([[1, 0, 0, 1, 0, 0],
                      [0, 1, 0, 0, 1, 0],
                      [0, 0, 1, 0, 0, 1],
                      [0, 0, 0, 1, 0, 0],
                      [0, 0, 0, 0, 1, 0],
                      [0, 0, 0, 0, 0, 1]])

        # observation_matrices
        H = np.array([[1, 0, 0, 0, 0, 0],
                      [0, 1, 0, 0, 0, 0],
                      [0, 0, 1, 0, 0, 0]])


        # observation_covariance
        R = np.diag([1e-4, 1e-4, 100])**2

        initial_state_mean = np.hstack([measurements[0, :], 3*[0.]])

        initial_state_covariance = np.diag([1e-4, 1e-4, 50, 1e-6, 1e-6, 1e-6])**2

        kf = KalmanFilter(
          transition_matrices=F,
          observation_matrices=H,
          observation_covariance=R,
          initial_state_mean=initial_state_mean,
          initial_state_covariance=initial_state_covariance,
          em_vars=['transition_covariance']
        )


        Q = np.array([[  3.17720723e-09,  -1.56389148e-09,  -2.41793770e-07,
                         2.29258935e-09,  -3.17260647e-09,  -2.89201471e-07],
                      [  1.56687815e-09,   3.16555076e-09,   1.19734906e-07,
                         3.17314157e-09,   2.27469595e-09,  -2.11189940e-08],
                      [ -5.13624053e-08,   2.60171362e-07,   4.62632068e-01,
                        1.00082746e-07,   2.81568920e-07,   6.99461902e-05],
                      [  2.98805710e-09,  -8.62315114e-10,  -1.90678253e-07,
                         5.58468140e-09,  -5.46272629e-09,  -5.75557899e-07],
                      [  8.66285671e-10,   2.97046913e-09,   1.54584155e-07,
                         5.46269262e-09,   5.55161528e-09,   5.67122163e-08],
                      [ -9.24540217e-08,   2.09822077e-07,   7.65126136e-05,
                        4.58344911e-08,   5.74790902e-07,   3.89895992e-04]])

        Q = 0.5*(Q + Q.T)


        kf.transition_covariance = Q

        state_means, state_vars = kf.smooth(measurements)

        newData = data


        mX = []
        mY = []
        mA = []
        mB = []

        for i in range(len(data)):
          tempLatitude = state_means[i][1]
          tempLongitude = state_means[i][0]

          newData[i]['latitude'] = tempLatitude
          newData[i]['longitude'] = tempLongitude
          mX = np.append(mX,tempLatitude)
          mY = np.append(mY,tempLongitude)
          mA = np.append(mA,newData[i]["altitude"])
          mB = np.append(mB,newData[i]["datetime"])

        newCords = pd.DataFrame({
          'lat': mX,
          'lon': mY,
          'ele': mA,
          'time':mB
        }) 

        logging.info(newCords)

        finalResult = json.dumps(newData)
        logging.info(finalResult)

        return {'result':finalResult},200