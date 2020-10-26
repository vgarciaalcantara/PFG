/* Constants */

const thingsboardApiUrl= `//138.4.92.46:8080/api/`

// query selectors
const $$ = query => document.querySelectorAll(query)
const $ = query => document.querySelector(query) || undefined

const goToLogin = () => {
  const path = location.pathname
  if (path !== '/login' && path !== '/')
    location.href = '/login'
}


const thingsboard = {
  authToken: localStorage.getItem('token'),

  api: {
    apiCall: (path, params = { obj: {} }) => {
      document.body.classList.add('loading')
      return fetch(thingsboardApiUrl + path, Object.assign(params.obj || {}, {
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Authorization': params.token || thingsboard.authToken
        }
      })).then(response => {
        if (response.status === 401) {
          thingsboard.auth = null
          goToLogin()
        }
        document.body.classList.remove('loading')
        return response.json()
      }).catch(() => {
        thingsboard.auth = null
        goToLogin()
      })
    }
  }
}

thingsboard.api.auth = {
  login: (username, password) => thingsboard.api.apiCall('auth/login', {
    obj: {
      method: 'POST',
      body: JSON.stringify({ username, password })
    }
  }).then(({ token }) => token && `Bearer ${token}`),

  logout: () => {
    thingsboard.auth = null
    localStorage.removeItem('token')
    thingsboard.api.apiCall('auth/logout')
      .then(() => { goToLogin() })
  },

  getUser: () => thingsboard.api.apiCall('auth/user')
}

thingsboard.api.assets = {
  getUserAssets: () => Promise.all([
    fetch('/asset-relations.json')
      .then(response => response.json()),
    thingsboard.api.auth.getUser()
      .then(data => data.email)
  ]).then(([assetRelations, email]) => assetRelations[email])
}

thingsboard.api.devices = {
  getRoomDevices: (roomName, token) =>
    thingsboard.api
      .apiCall(`tenant/devices?pageSize=99&page=0&textSearch=${roomName}-`,
        { token })
      .then(({ data }) => data),
  getDeviceTelemetry: (deviceId, token) =>
    thingsboard.api
      .apiCall(
      `plugins/telemetry/DEVICE/${deviceId}/values/timeseries?useStrictDataTypes=false`,
        { token })
}



/* __MAIN__ */
window.onLoad = () => {
  const path = location.pathname
  if (!thingsboard.authToken)
    goToLogin()
}
