<!-- Meta tags -->
<script>
    document.title = 'Panel de control'
</script>

<!-- Content -->
<!--<main>-->
    <header>
        <div>
            <span id="user-icon"></span>
            <span id="user-name"></span>
        </div>
        <a class="__eth-link" href="/rooms" id="room-name">...</a>
        <hr>
        <div>
            <label class="checktext">
                <input type="radio" name="tab" autocomplete="off" checked>
                <span>Estadísticas</span>
            </label><!--
         --><label class="checktext">
                <input type="radio" name="tab" autocomplete="off">
                <span>&nbsp;Controles&nbsp;</span>
            </label>
        </div>
    </header>
    <div id="main-container" class="show-dashboard">
        <div>
            <div>
                <?php include('content/templates/dashboard-charts.html')?>
            </div>
            <div>
                <div id="controls">
                    <div id="termometer-widget">
                        <div id="termometer">
                            <span>MAX<br>21 ºC</span>
                        </div>
                        <div id="termometer-controls">
                            <input type="text" value="21 ºC" autocomplete="off" disabled>
                            <div>
                                <button id="decrease-temperature">-</button>
                                <button id="increase-temperature">+</button>
                            </div>
                        </div>
                    </div>
                    <div id="lights-widget">
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>
<!--</main>-->

<!-- Styles -->
<style>
header {
    font-size: 20px;
    text-align: center;
    line-height: 1.8em;
    color: #333;
}

#user-icon {
    display: inline-block;
    margin-bottom: -9px;
    height: 35px;
    width: 35px;
    background: url('/content/img/user-icon.svg') center no-repeat;
    background-size: 100%;
}

.checktext span { width: 120px; }

#room-name {
    font-family: 'Cantarell Extra Bold';
    color: inherit;
}

#main-container {
    overflow: hidden;
    transition: margin 1s;
}

#main-container.show-dashboard { margin-left: 0; }

#main-container.show-controls { margin-left: -100vw; }

#main-container > div {
    width: 200vw;
    display: flex;
}

#main-container > div > div { width: 100vw; }

#controls { margin-top: 20px; }

#termometer {
    width: 70%;
    background: url(/content/img/temp-20.svg) no-repeat center;
    background-size: 140%;
    max-width: 350px;
    margin: auto;
    position: relative;
    font-size: 12vw;
    transition: background .6s;
}

#termometer::after {
    content: '';
    display: block;
    padding-bottom: 100%;
}

#termometer::before {
    content: attr(data-temp)' ºC';
    display: block;
    position: absolute;
    color: #feae20;
    font-weight: bold;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -60%);
    text-shadow: 0 0 .2em;
}

#termometer span {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, 70%);
    font-size: 4vw;
}

#termometer-controls {
    text-align: center;
    margin: 20px;
}

#termometer-controls > div { display: inline-block; }

#termometer-controls input {
    width: 9em;
    text-align: center;
    font-weight: bold;
    margin: .5em;
    cursor: default;
}

#termometer-controls button {
    width: 3em;
    height: 3em;
    margin: .5em .35em;
    font-family: 'Cantarell Extra Bold'
}

#controls hr {
  width: 250px;
  margin: 40px auto;
}

@media (min-width: 390px) {
    #termometer { font-size: 47px; }
    #termometer span { font-size: 16px }
}
</style>

<!-- Scripts -->
<script>
__ETHENIS.onLoadOnce = () => {
  const roomName = window.location.pathname.replace('/dashboard', '').substr(1) ||
      window.localStorage.getItem('lastRoom')

  if (!roomName || roomName === 'null')
    __ETHENIS.loadPage('/rooms')

  // Set room name
  window.localStorage.setItem('lastRoom', roomName)
  window.history.pushState(null, null, `/dashboard/${roomName}`)
  $('#room-name').innerText = `Aula ${roomName}`

  // Get user data
  thingsboard.api.auth
    .getUser()
    .then(data => {
      const fullName = `${data.firstName || ''} ${data.lastName || ''}`
      $('#user-name').innerText = fullName
    })

  // Get room data
  thingsboard.api.devices
    .getRoomDevices(roomName)
    .then(devices => {
      devices.forEach(device => {
        thingsboard.api.devices
          .getDeviceTelemetry(device.id.id)
          .then(({ value: [{value}] }) => {
            dashboardChart.chartData[device.type].data = JSON.parse(value)
            if (device.type === 'co2')
              dashboardChart.drawChart(dashboardChart.chartData.co2)
          })
      })
    })



  /* ****************** */
  /*   TABS             */
  /* ****************** */
  $$('[name=tab]').forEach((e, i) => {
    e.onclick = () => {
      if (i === 0)
        $('#main-container').className = 'show-dashboard'
      else
        $('#main-container').className = 'show-controls'
    }
  })



  /* ****************** */
  /*   CONTROL WIDGETS  */
  /* ****************** */

  // Termometer
  const setTermometer = temp => {
    const termometer = $("#termometer")
    temp = Math.min(Math.max(temp, 11), 27)
    termometer.dataset.temp = temp
    const imageTemp = Math.trunc(temp)
    termometer.style.backgroundImage = `url(/content/img/temp-${imageTemp}.svg)`
  }

  $('#increase-temperature').onclick = () => {
    let temp = $('#termometer-controls input').value.match(/\d/g).join('')
    let newTemp = Math.min(parseInt(temp) + 1, 21)
    $('#termometer-controls input').value = `${newTemp} ºC`
  }

  $('#decrease-temperature').onclick = () => {
    let temp = $('#termometer-controls input').value.match(/\d/g).join('')
    let newTemp = Math.max(parseInt(temp) - 1, 16)
    $('#termometer-controls input').value = `${newTemp} ºC`
  }



  /* ****************** */
  /*   DEMO DATA        */
  /* ****************** */
  setTermometer(19)
}
</script>
