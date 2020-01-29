<nav id="menu" class="navbar navbar-expand-lg navbar-dark bg-primary">
      <a class="navbar-brand" href="#">Cheali</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li id="batlist" class="nav-item">
            <a class="nav-link" href="batlist.php">Battery List</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="action.html">Action</a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Active devices
          </a>
          <div id="chargers" class="dropdown-menu" aria-labelledby="navbarDropdown">
          </div>
      </li>
      <li id="packer" class="nav-item">
        <a class="nav-link" href="packer.html">Pack builder</a>
    </li>
</li>
<li class="nav-item">
    <a class="nav-link" href="settings.php">Settings</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="logout.php">Logout</a>
</li>
</ul>

<form class="form-inline my-2 my-lg-0" action="qrcode_read.html" target="_blank">
    <span class="form-control mr-sm-2">
      Current Battery:
  </span>
  <button class="btn btn-outline-success my-2 my-sm-0" value="Open QRCode">QRCode</button>
</form>
</div>
</nav>