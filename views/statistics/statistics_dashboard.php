<?php 
include_once('js/dashboard_js.php');
?>
<!-- DASHBOARD -->
<div class="container-fluid margin-rl">
  <div class"row">
    

    <!-- Widget Localização de usuários (Em tempo real) -->
    <div id="dash-local-usuarios" class="bgc-widget margin-r10px col-md-5 col-sm-5 col-lg-5 table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th class="text-left"> <?php _e('Users\' Location', 'tainacan') ?> </th>
          </tr>
        </thead>
        <tbody id="tbody-local-usuarios">
          <tr>
            <td>
              <iframe height="347px" width="100%" scrolling="no" frameborder="0" src="<?php echo get_template_directory_uri() . '/views/statistics/inc/maps-local-usuario.html' ?>"></iframe>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Widget Buscas Frequentes -->
    <div id="dash-buscas-frequentes" class="bgc-widget margin-r10px col-md-3 col-sm-3 col-lg-3 table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th class="text-left"> <?php _e('Frequent Searches', 'tainacan'); ?> </th>
            <th class="text-right">
              <a href="javascript:void(0)" id="refresh-buscas-frequentes" class="glyphicon glyphicon-refresh"></a>
            </th>
          </tr>
        </thead>
        <tbody id="tbody-buscas-frequentes">
          <!-- Conteúdo dinâmico, vindo de dashboard_js.php -->
        </tbody>
      </table>
    </div>

    <!-- Widget a definir -->
    <div id="" class="bgc-widget col-md-3 col-sm-3 col-lg-3 table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th class="text-left"> <?php _e('No name', 'tainacan'); ?> </th>
            <th class="text-right">
              <a href="javascript:void(0)" id="" class="glyphicon glyphicon-refresh"></a>
            </th>
          </tr>
        </thead>
          <tr>
            <td height="370px">
              <!-- Conteúdo dinâmico, vindo de dashboard_js.php -->
            </td>
          </tr>
      </table>
    </div>

    <!-- Widget a definir -->
    <div id="" class="bgc-widget margin-t10px margin-r10px col-md-3 col-sm-3 col-lg-3 table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th class="text-left"> <?php _e('No name', 'tainacan'); ?> </th>
            <th class="text-right">
              <a href="javascript:void(0)" id="" class="glyphicon glyphicon-refresh"></a>
            </th>
          </tr>
        </thead>
        <tbody id="">
          <tr>
            <td height="310px">
              <!-- Conteúdo dinâmico, vindo de dashboard_js.php -->
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Widget Eventos -->
    <div id="dash-eventos" class="bgc-widget margin-t10px margin-r10px col-md-3 col-sm-3 col-lg-3 table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th class="text-left"> <?php _e('Events', 'tainacan'); ?> </th>
            <th class="text-right">
              <a href="javascript:void(0)" id="refresh-eventos" class="glyphicon glyphicon-refresh"></a>
            </th>
          </tr>
        </thead>
        <tbody id="tbody-eventos">
          <tr>
            <td height="310px">
              <!-- Conteúdo dinâmico, vindo de dashboard_js.php -->
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Widget Perfis Usuário -->
    <div id="dash-perfis-usuario" class="bgc-widget margin-t10px col-md-5 col-sm-5 col-lg-5 table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th class="text-left"> <?php _e('Users by profile', 'tainacan')?> </th>
            <th class="text-right">
              <a href="javascript:void(0)" id="refresh-perfis-usuario" class="glyphicon glyphicon-refresh"></a>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td id="gChart-perfis-usuario" width="100%" height="310px">
              <!-- Gráfico dinâmico, vindo de dashboard_js.php -->
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</div>