<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'Brand List Login History',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">

        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
        <div class="col-md-10 col-md-offset-2 main">
            <ol class="breadcrumb">
                <li><a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_list', array(), array(), '', true)); ?>">Brand List</a></li>
                <li class="active">Login History</a></li>
            </ol>
            <h1 class="page-header">Login History List</h1>
            <h4>Login Total Count :<?php assign($data['login_count']);?></h4>
            <div class="jsMessageSettingTarget" style="display:none">
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Last Login Date</th>
                        <th>Name</th>
                        <th>User ID</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $service_factory = new aafwServiceFactory();
                    /** @var UserService $service */
                    $User_service = $service_factory->create('UserService')?>
                    <?php foreach($data['pager'] as $login_detail):?>
                        <tr>
                            <td><?php assign($login_detail->login_date);?></td>
                            <td><?php assign($User_service->getUserByBrandcoUserId($login_detail->user_id)->name);?></td>
                            <td><?php assign($login_detail->user_id);?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                    'TotalCount' => $data['allBrandCount'],
                    'CurrentPage' => $this->params['p'],
                    'Count' => $data['pageLimited'],
                ))) ?>
            </div>
        </div>
    </div><!-- row -->
</div><!-- container-fluid -->

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
