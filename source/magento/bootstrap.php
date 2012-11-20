<?php


  function tmo_magento_bootstrap()
  {
    if(@is_file(MAGENTO_ROOT.'/app/etc/local.php'))
      require_once MAGENTO_ROOT.'/app/etc/local.php';

    session_start();

    if(false===strpos($_SERVER['REQUEST_URI'], '/admin'))
    {
      Mage::init();

      $model=Mage::getResourceModel('core/website');
      $stmt=$model->getReadConnection()->query(sprintf(
        'SELECT code FROM %1$s WHERE name = \'%2$s\'',
          $model->getMainTable(),
          strtolower(trim($_SERVER['HTTP_HOST']))
      ));

      if(!$website=$stmt->fetch(PDO::FETCH_COLUMN, 0))
      {
        $stmt=$model->getReadConnection()->query(sprintf(
          'SELECT code FROM %1$s WHERE is_default = 1',
            $model->getMainTable()
        ));

        $website=$stmt->fetch(PDO::FETCH_COLUMN, 0);
      }

      Mage::run($website, 'website');
    }
    else
    {
      Mage::run('admin', 'store');
    }
  }
?>
