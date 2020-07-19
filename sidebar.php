<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index">
  <div class="sidebar-brand-icon rotate-n-15">
    <i class="fas fa-tractor"></i>
  </div>
  <div class="sidebar-brand-text mx-3">Milkow <sup>0.4</sup></div>
</a>

<!-- Divider -->
<hr class="sidebar-divider my-0">

<!-- Nav Item - Dashboard -->
<li class="nav-item <?php if ($pageTitle == 'Tableau de bord'){echo 'active';}?>">
  <a class="nav-link" href="index">
    <i class="fas fa-fw fa-tachometer-alt"></i>
    <span>Tableau de bord</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
  Étable
</div>

<!-- Nav Item - Cow Manager -->
<li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'], 'cows-manager') || stripos($_SERVER['REQUEST_URI'], 'add-cow')){echo 'active';}?>">
  <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCows" aria-expanded="true" aria-controls="collapsePages">
    <i class="fad fa-cow"></i>
    <span>Bovins</span>
  </a>
  <div id="collapseCows" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
    <div class="bg-white py-2 collapse-inner rounded">
      <h6 class="collapse-header">Gestion :</h6>
      <a class="collapse-item" href="cows-manager">Géger les bovins</a>
      <a class="collapse-item" href="add-cow">Ajouter un bovin</a>
    </div>
  </div>
</li>

<!-- Nav Item - Tables -->
<li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'], 'gestations')){echo 'active';}?>">
  <a class="nav-link" href="/gestations">
  <i class="fas fa-baby-carriage"></i>
    <span>Gestations</span></a>
</li>

<!-- Nav Item - Tables -->
<li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'], 'treats')){echo 'active';}?>">
  <a class="nav-link" href="/treats">
  <i class="fas fa-syringe"></i>
    <span>Traitements</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
  Lait
</div>

<!-- Nav Item - Tables -->
<li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'], 'laboratory')){echo 'active';}?>">
  <a class="nav-link" href="laboratory">
  <i class="fas fa-flask"></i>
    <span>Laboratoire</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Heading -->
<div class="sidebar-heading">
  Bureau
</div>


<!-- Nav Item - Tables -->
<li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'], 'sales')){echo 'active';}?>">
  <a class="nav-link" href="/sales">
  <i class="fas fa-cash-register"></i>
    <span>Ventes</span></a>
</li>

<!-- Nav Item - Cow Manager -->
<li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'], 'archives') || stripos($_SERVER['REQUEST_URI'], 'dead') || stripos($_SERVER['REQUEST_URI'], 'sold')){echo 'active';}?>">
  <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseArchives" aria-expanded="true" aria-controls="collapsePages">
    <i class="fas fa-archive"></i>
    <span>Archives</span>
  </a>
  <div id="collapseArchives" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
    <div class="bg-white py-2 collapse-inner rounded">
      <a class="collapse-item" href="/sold">Bovins vendus</a>
      <a class="collapse-item" href="/dead">Bovins décédés</a>
      <a class="collapse-item" href="archives">Corbeille</a>
    </div>
  </div>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
  <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>

</ul>
<!-- End of Sidebar -->