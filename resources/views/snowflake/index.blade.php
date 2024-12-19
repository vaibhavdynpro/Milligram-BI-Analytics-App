@extends('layouts.app')

@section('content')
<!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <!-- Theme style -->
<section class="content">
<div class="row" style="margin-left:4.2%;margin-top:3%">
          <div class="col-lg-3 col-3">
            <!-- small box -->
            <div class="small-box bg-info" style="margin-left:4.2%">
              <div class="inner">
                <h4>Data Preprocessing</h4>

                <!-- <p>Data Preprocessing</p> -->
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="{{ route('dataprocessing') }}" class="small-box-footer">CLICK <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-3">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h4>Post Elt Process</h4>

                <!-- <p>Data Preprocessing</p> -->
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">CLICK <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-3">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h4>Staging Preparation</h4>

                <!-- <p>Data Preprocessing</p> -->
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">CLICK <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-3">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h4>Data Preprocessing</h4>

                <!-- <p>Data Preprocessing</p> -->
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">CLICK <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          
</div>
</section>
  <!-- /.content-wrapper -->
@endsection
<!-- DataTables -->
<script src="{{ asset('js/app.js') }}" ></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

<script>

</script>
