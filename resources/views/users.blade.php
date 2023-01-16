@include('header')

<div id="main-content" class="row"> <!-- Main Container -->

  <div id="admin-content" class="col-lg-10">

    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active"><a href="#student" aria-controls="student" role="tab" data-toggle="tab">Student</a></li>
      <li role="presentation" ><a href="#teacher" aria-controls="teacher" role="tab" data-toggle="tab">Teacher</a></li>
    </ul> <!-- End of Nav tabs-->
   
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="student" style="margin-top:20px;">
        <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered" id="u-student-table">
          <thead>
            <tr>
              <th id="th">Id</th>
              <th id="th">Name</th>
              <th id="th">Prodi</th>
              <th id="th">Fakultas</th>
              <th id="th">Point</th>
              <th>Action</th>
            </tr>
          </thead>
          <tfoot style="display: table-header-group;">
            <tr>
                <th id="th">Id</th>
                <th id="th">Name</th>
                <th id="th">Prodi</th>
                <th id="th">Fakultas</th>
                <th id="th">Point</th>
                <th id="th"></th>
            </tr>
          </tfoot>
        </table>  
      </div>
      <!-- End of student tab panel  -->
      <div role="tabpanel" class="tab-pane" id="teacher" style="margin-top:20px;">
        <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered" id="u-teacher-table">
          <thead>
            <tr>
              <th id="th">Id</th>
              <th id="th">Name</th>
              <th id="th">Prodi</th>
              <th id="th">Fakultas</th>
              <th id="th">Point</th>
              <th>Action</th>
            </tr>
          </thead>
          <tfoot style="display: table-header-group;">
            <tr>
                <th id="th">Id</th>
                <th id="th">Name</th>
                <th id="th">Prodi</th>
                <th id="th">Fakultas</th>
                <th id="th">Point</th>
                <th id="th"></th>
            </tr>
          </tfoot>
        </table>  
      </div>
      <!-- End of teacher tab panel  -->
    </div>

  </div>

  </div> <!-- End of Main Container -->

  <!-- Modal -->
  <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">

      <div class="modal-content">
        <div id="loading-content" style="z-index: 1; height: 100%; width: 100%; overflow: auto; margin: auto; position: absolute; top: 0; left: 0; bottom: 0; right: 0; background: rgba(0, 0, 0, .5);" ></div>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">User detail</h4>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">User id</label>
                  <input type="text" class="form-control" id="inputUserId" name="inputUserId" placeholder="" value="" disabled>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Name</label>
                  <input type="text" class="form-control" id="inputName" name="inputName" placeholder="" value="" disabled>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Prodi</label>
                  <input type="text" class="form-control" id="inputProdi" name="inputProdi" placeholder="" value="" disabled>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Fakultas</label>
                  <input type="text" class="form-control" id="inputFakultas" name="inputFakultas" placeholder="" value="" disabled>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Points</label>
                  <input type="text" class="form-control" id="inputPoint" name="inputPoint" placeholder="" value="" disabled>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Type</label>
                  <input type="text" class="form-control" id="inputType" name="inputType" placeholder="" value="" disabled>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Api Key</label>
                  <input type="text" class="form-control" id="inputApiKey" name="inputApiKey" placeholder="" value="" disabled>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Device Id</label>
                  <input type="text" class="form-control" id="inputDeviceId" name="inputDeviceId" placeholder="" value="" disabled>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Device Type</label>
                  <input type="text" class="form-control" id="inputDeviceType" name="inputDeviceType" placeholder="" value="" disabled>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Device Model</label>
                  <input type="text" class="form-control" id="inputDeviceModel" name="inputDeviceModel" placeholder="" value="" disabled>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Version Code</label>
                  <input type="text" class="form-control" id="inputVersionCode" name="inputVersionCode" placeholder="" value="" disabled>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Create time</label>
                  <input type="text" class="form-control" id="inputCreateTime" name="inputCreateTime" placeholder="" value="" disabled>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade bs-example-modal-sm" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">Please wait..</h4>
        </div>
        <div class="modal-body">
          <div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
            </div>
          </div>
        </div>
        <!-- <div class="modal-footer"></div> -->
      </div>
    </div>
  </div>
  

  <script type="text/javascript" src="http://127.0.0.1:8000/amtanesia/resources/js/spin.min.js"></script>
  <script type="text/javascript">
  $(document).ready(function() {
    
    $('#menu-users-manager').addClass('active');

    var student_table = $('#u-student-table').DataTable({
      "processing": true,
      "serverSide": false,
      "ajax": {
        "type" : "GET",
        "url" : "http://127.0.0.1:8000/amtanesia/index.php/admin/users/students",
        "data" : {
          type: "student"
        }
      },
      'bAutoWidth': false,
      "columnDefs": [ {
          "targets": -1,
          "data": null,
          "defaultContent": '<button class="btn btn-default row-btn-view" id="btn-show-detail" title="View"> <span class="glyphicon glyphicon-eye-open"></span></button>'
      } ]
    });

    var teacher_table = $('#u-teacher-table').DataTable({
      "processing": true,
      "serverSide": true,
      "ajax": {
        "type" : "GET",
        "url" : "http://127.0.0.1:8000/amtanesia/index.php/admin/users/students",
        "data" : {
          type: "teacher"
        }
      },
      'bAutoWidth': false,
      "columnDefs": [ {
          "targets": -1,
          "data": null,
          "defaultContent": '<button class="btn btn-default row-btn-view" id="btn-show-detail" title="View"> <span class="glyphicon glyphicon-eye-open"></span></button>'
      } ]
    });

    $('#u-student-table tfoot th').not(":eq(5)").each( function () {
        var title = $('#elearn-his-table thead th').eq($(this).index()).text();
        $(this).html('<input style="width: 100%; padding: 3px; box-sizing: border-box;" type="text" placeholder="Search ..." />');
    } );

    $('#u-teacher-table tfoot th').not(":eq(5)").each( function () {
        var title = $('#elearn-his-table thead th').eq($(this).index()).text();
        $(this).html('<input style="width: 100%; padding: 3px; box-sizing: border-box;" type="text" placeholder="Search ..." />');
    } );

    student_table.columns().eq( 0 ).each( function ( colIdx ) {
      if (colIdx == 5) return;

      $( 'input', student_table.column(colIdx).footer()).on('keyup change', function () {
          student_table
                         .column(colIdx)
                         .search( this.value )
                         .draw();
      } );

    } );

    teacher_table.columns().eq( 0 ).each( function ( colIdx ) {
      if (colIdx == 5) return;

      $( 'input', teacher_table.column(colIdx).footer()).on('keyup change', function () {
          teacher_table
                         .column(colIdx)
                         .search( this.value )
                         .draw();
      } );

    } );
    
    function callAjaxGetDetailUser(user_id){
      $.ajax({
        type: "POST",
        url: "http://127.0.0.1:8000/amtanesia/index.php/admin/users/detail",
        dataType: "json",
        data: { userid : user_id},
        success: function(data, textStatus, xhr){
          setTimeout(function() {
              $('#loading-content').hide();
              fillDataUserDetails(data);
          }, 2000);
        }
      });
    }

    function removeAllValueDataUserDetails(){
      document.getElementById('inputUserId').value = document.getElementById('inputName').value =  document.getElementById('inputProdi').value = document.getElementById('inputFakultas').value = document.getElementById('inputPoint').value = document.getElementById('inputCreateTime').value = document.getElementById('inputApiKey').value = document.getElementById('inputDeviceId').value = document.getElementById('inputDeviceType').value = document.getElementById('inputDeviceModel').value = document.getElementById('inputVersionCode').value = document.getElementById('inputType').value = "";
    }

    function fillDataUserDetails(data){
      document.getElementById('inputUserId').value = data[0]['ID_USERS'];
      document.getElementById('inputName').value = data[0]['NAME'];
      document.getElementById('inputProdi').value = data[0]['PRODI'];
      document.getElementById('inputFakultas').value = data[0]['FAKULTAS'];
      document.getElementById('inputPoint').value = data[0]['POINTS'];
      document.getElementById('inputCreateTime').value = data[0]['CREATE_TIME'];
      document.getElementById('inputApiKey').value = data[0]['API_KEY'];
      document.getElementById('inputDeviceId').value = data[0]['DEVICE_ID'];
      document.getElementById('inputDeviceType').value = data[0]['DEVICE_TYPE'];
      document.getElementById('inputDeviceModel').value = data[0]['DEVICE_MODEL'];
      document.getElementById('inputVersionCode').value = data[0]['VERSION_CODE'];
      document.getElementById('inputType').value = data[0]['TYPE'];
    }

    $('#u-student-table').on('click', '#btn-show-detail', function(e){
      var row = $(event.target).closest("tr").get(0);
      var student_id = $("#u-student-table").dataTable().fnGetData(row)[0];
      var spinner = new Spinner().spin()
      var div = document.getElementById('loading-content');
      div.appendChild(spinner.el);
      $('#viewModal').modal('show');
      $('#loading-content').show();
      removeAllValueDataUserDetails();
      callAjaxGetDetailUser(student_id);
    });

    $('#u-teacher-table').on('click', '#btn-show-detail', function(e){
      var row = $(event.target).closest("tr").get(0);
      var teacher_id = $("#u-teacher-table").dataTable().fnGetData(row)[0];
      var spinner = new Spinner().spin()
      var div = document.getElementById('loading-content');
      div.appendChild(spinner.el);
      $('#viewModal').modal('show');
      $('#loading-content').show();
      removeAllValueDataUserDetails();
      callAjaxGetDetailUser(teacher_id);
    });
    
  } );
  </script>

</body>
</html>