<?php
 
session_start();
include 'includes/database.php';
include 'includes/forbidden.php';

$pageTitle = 'A simple page';
include 'header.php';

?>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

<?php include 'sidebar.php'; ?>
<?php include 'topbar.php'; ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800"><?= $pageTitle ?></h1>

        </div>
        <!-- /.container-fluid -->

        <?php
// Solution 1


$dateSlash = '02/12/1991';
$today = date('d-m-Y');
echo $today;
$dateTiret = str_replace('/', '-', $dateSlash);
echo '<br>';
$dateConvert =  date('Y-m-d', strtotime($dateTiret));
echo $dateConvert;

echo '<br>';
echo '<br>';

$dateTiret1 = new DateTime($dateTiret);
$dateTiret2 = new DateTime('30-12-1991');
$diff = $dateTiret2->diff($dateTiret1)->format("%a");
echo $diff;

echo '<br>';
echo '<br>';

// Solution 2
$debut = strtotime('1991-12-02');
$fin = strtotime('2020-06-29');
$dif = ceil(abs($fin - $debut) / 86400);
echo $dif;

echo '<br>';
echo '<br>';

// Solution 3
$datetime1 = new DateTime($dateTiret);
$datetime2 = new DateTime($today);
$difference = $datetime1->diff($datetime2);
echo $difference->y.' Années, '.$difference->m.' Mois, '.$difference->d.' Jours';

?>


<?php include 'footer.php'; ?>