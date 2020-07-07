</div>
<!-- End of Main Content -->

<?php if (!stripos($_SERVER['REQUEST_URI'], 'login') & !stripos($_SERVER['REQUEST_URI'], 'login.php') & !stripos($_SERVER['REQUEST_URI'], 'register') & !stripos($_SERVER['REQUEST_URI'], 'register.php') & !stripos($_SERVER['REQUEST_URI'], 'forgot-password') & !stripos($_SERVER['REQUEST_URI'], 'forgot-password.php')) { ?>
<!-- Footer -->
<footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy;2020 - Créé par Jean-Baptiste BAUD</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->


  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Deconnexion ?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Cliquez sur "Se déconnecter" ci-dessous pour confirmer.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
          <a class="btn btn-danger" href="logout.php">Se déconnecter</a>
        </div>
      </div>
    </div>
  </div>
<?php } // END IF PAGE ?> 

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <script src="vendor/chart.js/Chart.min.js"></script>

  <script src="js/demo/chart-area-demo.js"></script>
  <script src="js/demo/chart-pie-demo.js"></script>

  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="vendor/datatables/fixedHeader.bootstrap4.min.js"></script>

  <script src="vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
  <script src="vendor/bootstrap-datepicker/js/bootstrap-datepicker.fr.min.js"></script>
  <script src="js/bootstrap-datepicker/bootstrap-datepicker.js"></script>

  <script src="vendor/bootstrap-select/js/bootstrap-select.min.js"></script>
  <script src="vendor/bootstrap-select/js/defaults-fr_FR.min.js"></script>

  <script src="js/scripts.js"></script>

</body>

</html>