const API_TOKEN = 'Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJwcm9mZXNvcjJAdXBtLmVzIiwic2NvcGVzIjpbIlRFTkFOVF9BRE1JTiJdLCJ1c2VySWQiOiI4NWUxMzVkMC1mYzRhLTExZWEtOThlYS0yMTg5M2FkN2ZiZTkiLCJmaXJzdE5hbWUiOiJFc3RyZWxsYSIsImxhc3ROYW1lIjoiU2FlbnoiLCJlbmFibGVkIjp0cnVlLCJpc1B1YmxpYyI6ZmFsc2UsInRlbmFudElkIjoiM2NiMTNmOTAtZmM0YS0xMWVhLTk4ZWEtMjE4OTNhZDdmYmU5IiwiY3VzdG9tZXJJZCI6IjEzODE0MDAwLTFkZDItMTFiMi04MDgwLTgwODA4MDgwODA4MCIsImlzcyI6InRoaW5nc2JvYXJkLmlvIiwiaWF0IjoxNjAwNzIxMDM4LCJleHAiOjE2MzIyNTcwMzh9.4srowuJCf-fDf_RzdyFzOApaDgbGk1zJWR8VJ9oIKDHI3ItNoYRk-wNyga8_o-dgwpNzHZ5tPmyRUYLZBo0SjQ'
const API_URL = '//138.4.92.46:8080/api/'
const ROOMS = ['general', 4013, 4002, 4106,  4107, 4003, 4012, 4004, 4110, 4105 ]
const SENSORS = ['co2', 'water', 'pollution', 'energy', 'noise']
const CHART_DAYS = 14

const generateRandomData = () =>
  Array(CHART_DAYS).fill().map((_, x) => 0.05 * x^2 + 20 * x * Math.random())

const apiCall = (path, obj = {}) => 
  fetch(API_URL + path, Object.assign(obj, {
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Authorization': API_TOKEN
    }
  }))

let timeout = 0
const TIMEOUT_INTERVAL = 50
const apiCallWithTimeout = (...params) => new Promise(resolve => {
  timeout += TIMEOUT_INTERVAL
  setTimeout(() => resolve(apiCall(...params)), timeout)
})

Promise.all(ROOMS.map(roomName =>
  Promise.all(SENSORS.map(sensor =>
    apiCallWithTimeout('device', {
      method: 'POST',
      body: JSON.stringify({
        name: roomName + '-' + sensor,
        type: sensor
      })
    })))))
.then(() => apiCall('tenant/deviceInfos?pageSize=9999&page=0')
  .then(response => response.json())
  .then(({ data: devices }) => devices.forEach(({ id: { id: deviceId } }) =>
    apiCallWithTimeout(`device/${deviceId}/credentials`)
    .then(response => response.json())
    .then(({ credentialsId }) =>
      apiCallWithTimeout(`v1/${credentialsId}/telemetry`, {
        method: 'POST',
        body: JSON.stringify({ value: generateRandomData() })
      })))))
