<header class="main-header">
    <!-- Logo -->
    <a href="#" title="<?php echo $cfg['header_logo']; ?>" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><?php echo $cfg['header_min']; ?></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><?php echo $cfg['header_max']; ?></span>
    </a>
    
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" title="Expandir/Diminuir o menu" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
            <?php
                switch($_SESSION['key']) {
                    case 'A':
                        echo'
                        <li class="bg-default">
                            <a class="toggle-search" href="#" title="Busque qualquer coisa dentro do programa"><i class="fa fa-search"></i> <span class="hidden-xs">Buscar</span></a>
                        </li>
                        <li class="bg-success">
                            <a href="backup" title="Back up do programa"><i class="fa fa-database"></i> <span class="hidden-xs">Back up</span></a>
                        </li>
                        <li class="bg-danger">
                            <a href="sair" title="Sair do programa"> Hi '.$_SESSION['seller'].' &#124; <i class="fa fa-sign-out"></i> Sair</a>
                        </li>';
                        break;
                    
                    case 'R':
                        echo'
                        <li class="bg-default">
                            <a class="toggle-search" href="#" title="Busque qualquer coisa dentro do programa"><i class="fa fa-search"></i> <span class="hidden-xs">Buscar</span></a>
                        </li>
                        <li class="bg-info">
                            <a href="log" title="Logs do sistema"><i class="fa fa-terminal"></i> <span class="hidden-xs">Log</span></a>
                        </li>
                        <li class="bg-success">
                            <a href="backup" title="Back up do programa"><i class="fa fa-database"></i> <span class="hidden-xs">Back up</span></a>
                        </li>
                        <li class="bg-danger">
                            <a href="sair" title="Sair do programa"> Hi '.$_SESSION['seller'].' &#124; <i class="fa fa-sign-out"></i> Sair</a>
                        </li>';
                        break;
                    
                    case 'U':
                        echo'
                        <li class="bg-default">
                            <a class="toggle-search" href="#" title="Busque qualquer coisa dentro do programa"><i class="fa fa-search"></i> <span class="hidden-xs">Buscar</span></a>
                        </li>
                        <!--<li class="bg-warning">
                            <a href="profile" title="Meus dados"><i class="fa fa-user"></i> Meus dados</a>
                        </li>-->
                        <li class="bg-success">
                            <a href="backup" title="Back up do programa"><i class="fa fa-database"></i> <span class="hidden-xs">Back up</span></a>
                        </li>
                        <li class="bg-danger">
                            <a href="sair" title="Sair do programa"> Hi '.$_SESSION['seller'].' &#124; <i class="fa fa-sign-out"></i> Sair</a>
                        </li>';
                        break;
                }
            ?>    
                
            </ul>
        </div>
    </nav>
</header>

<div class="page-search">
    <div class="tb">
        <span class="page-search-close">&times;</span>
        <div class="tb-cell">
            <form>
                <input type="search" id="search-keyword" title="Busque qualquer coisa dentro do programa" placeholder="Busque qualquer coisa dentro do programa">
                <div id="search-result" class="search-result"></div>
            </form>
        </div>
    </div>
</div>

<div class="page-load">
    <div class="tb">
        <span class="page-load-close">&times;</span>
        <div class="tb-cell">
            <img src="img/rings-black.svg" title="Carregando" alt="Carregando">
        </div>
    </div>
</div>

<div class="modal fade modal-search" id="modal-edit-pedido-search" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal fade modal-search" id="modal-edit-cliente-search" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal fade modal-search" id="modal-edit-praca-search" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal fade modal-search" id="modal-edit-produto-search" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal fade modal-search" id="modal-edit-vendedor-search" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>