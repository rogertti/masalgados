<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
        <?php
            if($_SESSION['key'] == 'A') {
                switch($m) {
                    case 1:
                        echo'
                        <li class="active">
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li>
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;
                    
                    case 2:
                        echo'
                        <li>
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li class="active">
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li>
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;
                    
                    case 3:
                        echo'
                        <li>
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li class="active">
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li>
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;
                    
                    case 4:
                        echo'
                        <li>
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li class="active">
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;
                    
                    case 5:
                        echo'
                        <li>
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li>
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li class="active">
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;
                    
                    case 6:
                        echo'
                        <li>
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li>
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li class="active">
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;

                    default:
                        echo'
                        <li>
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li>
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;
                }
            }

            if($_SESSION['key'] == 'R') {
                switch($m) {
                    case 1:
                        echo'
                        <li class="active">
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li>
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;
                    
                    case 2:
                        echo'
                        <li>
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li class="active">
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li>
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;
                    
                    case 3:
                        echo'
                        <li>
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li class="active">
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li>
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;
                    
                    case 4:
                        echo'
                        <li>
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li class="active">
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;
                    
                    case 5:
                        echo'
                        <li>
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li>
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li class="active">
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;
                    
                    case 6:
                        echo'
                        <li>
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li>
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li class="active">
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;

                    default:
                        echo'
                        <li>
                            <a href="inicio-adm" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="praca" title="Gerencie as pra&ccedil;as"><i class="fa fa-map-marker"></i> <span>Pra&ccedil;as</span></a>
                        </li>
                        <li>
                            <a href="produto" title="Gerencie os produtos"><i class="fa fa-tag"></i> <span>Produtos</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="vendedor" title="Gerencie os vendedores"><i class="fa fa-user"></i> <span>Vendedores</span></a>
                        </li>';
                        break;
                }
            }

            if($_SESSION['key'] == 'U') {
                switch($m) {
                    case 1:
                        echo'
                        <li class="active">
                            <a href="inicio" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="profile" title="Meus dados"><i class="fa fa-user"></i> <span>Meus dados</span></a>
                        </li>';
                        break;
                    
                    case 2:
                        echo'
                        <li>
                            <a href="inicio" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li class="active">
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="profile" title="Meus dados"><i class="fa fa-user"></i> <span>Meus dados</span></a>
                        </li>';
                        break;
                    
                    case 3:
                        echo'
                        <li>
                            <a href="inicio" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li class="active">
                            <a href="profile" title="Meus dados"><i class="fa fa-user"></i> <span>Meus dados</span></a>
                        </li>';
                        break;
                    
                    case 5:
                        echo'
                        <li>
                            <a href="inicio" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li class="active">
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="profile" title="Meus dados"><i class="fa fa-user"></i> <span>Meus dados</span></a>
                        </li>';
                        break;

                    default:
                        echo'
                        <li>
                            <a href="inicio" title="In&iacute;cio"><i class="fa fa-window-maximize"></i> <span>In&iacute;cio</span></a>
                        </li>
                        <li>
                            <a href="cliente" title="Gerencie os clientes"><i class="fa fa-user"></i> <span>Clientes</span></a>
                        </li>
                        <li>
                            <a href="romaneio" title="Gerencie os romaneios"><i class="fa fa-file-text"></i> <span>Romaneio</span></a>
                        </li>
                        <li>
                            <a href="profile" title="Meus dados"><i class="fa fa-user"></i> <span>Meus dados</span></a>
                        </li>';
                        break;
                }
            }
        ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>