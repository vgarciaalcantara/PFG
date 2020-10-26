<!-- Meta tags -->
<script>
    document.title = 'Estadísticas'
</script>

<!-- Content -->
<!--<main>-->
    <header>
        <div id="logo-etsisi"></div>
        <a href="/login">Iniciar sesión</a>
        <hr>
    </header>
    <?php include('content/templates/dashboard-charts.html')?>
<!--</main>-->

<!-- Styles -->
<style>
    header {
        text-align: center;
    }

    #logo-etsisi {
        background-image: url(/content/img/logo-etsisi.png);
        height: 100px;
        width: 200px;
        background-size: 100% auto;
        margin: auto;
        margin-bottom: 20px;
        transform: translateX(32px);
    }

    header hr { margin: 20px auto 70px }
</style>

<!-- Scripts -->
<script>
  __ETHENIS.onLoadOnce = () => {
    if (!thingsboard.authToken)
      $('nav').remove()
    else
      $('header a').remove()
    // Get room data
    thingsboard.api.auth.login('public@thingsboard.org', 'public')
    .then(token => thingsboard.api.devices
      .getRoomDevices('general', token)
      .then(devices => {
        devices.forEach(device => {
          thingsboard.api.devices
          .getDeviceTelemetry(device.id.id, token)
          .then(({ value: [{value}] }) => {
            dashboardChart.chartData[device.type].data = JSON.parse(value)
            if (device.type === 'co2')
              dashboardChart.drawChart(dashboardChart.chartData.co2)
          })
        })
      }))
  }
</script>
