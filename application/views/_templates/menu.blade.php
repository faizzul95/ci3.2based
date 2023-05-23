 <!-- ========== App Menu ========== -->
 <div class="app-menu navbar-menu">
 	<!-- LOGO -->
 	<div class="navbar-brand-box">
 		<!-- Dark Logo-->
 		<a href="javascript:void(0);" class="logo logo-dark">
 			<span class="logo-sm">
 				<img src="{{ currentCompanyLogo() }}" alt="" height="22">
 			</span>
 			<span class="logo-lg">
 				<img src="{{ currentCompanyLogo() }}" alt="" height="50">
 			</span>
 		</a>
 		<!-- Light Logo-->
 		<a href="javascript:void(0);" class="logo logo-light">
 			<span class="logo-sm">
 				<img src="{{ currentCompanyLogo() }}" alt="" height="30">
 			</span>
 			<span class="logo-lg">
 				<img src="{{ currentCompanyLogo() }}" alt="" height="60">
 			</span>
 		</a>
 		<button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
 			<i class="ri-record-circle-line"></i>
 		</button>
 	</div>

 	<div id="scrollbar">
 		<div class="container-fluid">
 			<div id="two-column-menu"></div>
 			<ul class="navbar-nav" id="navbar-nav">
 				<?php menuList(); ?>
 			</ul>
 		</div>
 		<!-- Sidebar -->
 	</div>
 </div>
 <!-- Left Sidebar End -->
 <!-- Vertical Overlay-->
 <div class="vertical-overlay"></div>