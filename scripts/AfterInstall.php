<?php


class AfterInstall
{
  protected $container;	
  public function run($container)
  {
    $config = $container->get('config');
 
    $tabList = $config->get('tabList');
    if (!in_array('Wischat', $tabList)) {
      $tabList[] = 'Wischat';
      $config->set('tabList', $tabList);
    }
	  if (!in_array('GruposWhatsapp', $tabList)) {
      $tabList[] = 'GruposWhatsapp';
      $config->set('tabList', $tabList);
    }
 
    $config->save();
  }
}