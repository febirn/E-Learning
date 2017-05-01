<?php include 'header_dashboard.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="container">

      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="../../dist/img/user4-128x128.jpg" alt="User profile picture">

              <h3 class="profile-username text-center">Mamang</h3>

              <p class="text-muted text-center">Lecture</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Name</b> <a class="pull-right">Mamang</a>
                </li>
                <li class="list-group-item">
                  <b>Email</b> <a class="pull-right">Mamang@gmail.com</a>
                </li>
                <li class="list-group-item">
                  <b>Phone</b> <a class="pull-right">32432432</a>
                </li>
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#activity" data-toggle="tab">Course</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="activity">
              <div class="well">
                  <h3><a href="dashboard_course_detail.php">Build a URL shortener</a></h3>
                  <p>Wait, URL shorteners are boring right? Nope. We're building a fully 
                  tested Lumen API, Vue.js client and implementing 301 redirects with Node.js and Express.</p>
            <br>
            <button type="button" class="btn btn-sm btn-default disabled">Slim</button>
            <button type="button" class="btn btn-sm btn-default disabled">Redis</button>
            <hr>
            <ul class="list-inline">
              <li>
              <i class="fa fa-tasks item"> 16 parts</i>
              </li>
              <li>
              <i class="fa fa-clock-o item"> 1 hour 41 min</i>
              </li>
              <li>
                <p class="item">6 days ago</p>
              </li>
            </ul>
              </div>

              <div class="well">
                  <h3><a href="dashboard_course_detail.php">Build a URL shortener</a></h3>
                  <p>Wait, URL shorteners are boring right? Nope. We're building a fully 
                  tested Lumen API, Vue.js client and implementing 301 redirects with Node.js and Express.</p>
            <br>
            <button type="button" class="btn btn-sm btn-default disabled">Slim</button>
            <button type="button" class="btn btn-sm btn-default disabled">Redis</button>
            <hr>
            <ul class="list-inline">
              <li>
              <i class="fa fa-tasks item"> 16 parts</i>
              </li>
              <li>
              <i class="fa fa-clock-o item"> 1 hour 41 min</i>
              </li>
              <li>
                <p class="item">6 days ago</p>
              </li>
            </ul>
              </div>

              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
  </div>
<?php include 'footer.php';
