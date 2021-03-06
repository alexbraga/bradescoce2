<?php
class Oitoo_Bradescocomercioeletronico2_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {

	  $this->loadLayout();
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("titlename", array(
                "label" => $this->__("Titlename"),
                "title" => $this->__("Titlename")
		   ));

      $this->renderLayout();

    }

    public function registrarBoletoAction(){

        $_order = mage::getModel('sales/order')->load($_REQUEST['id_pedido']);
        $_customer = mage::getModel('customer/customer')->load($_REQUEST['id_cliente']);

        if($_order && $_customer){
            $url_acesso = mage::helper('bradescoce2')->getUrlAcesso($_order, $_customer);
            if($url_acesso){
                var_dump($url_acesso);
            } else {
                echo "Não foi possível emitir o boleto. Por favor entre em contato!";
            }
        } else {
            echo "Não foi possível emitir o boleto. Por favor entre em contato!";
        }


    }



    public function ConfirmarPagamentosAction(){
          mage::getModel('bradescoce2/payment')->confirmarPagamentos();
    }


    public function confirmaPedidoAction(){
        //O bradesco faz uma chamada para essa função para confirmar que o pedido de geração do boleto realmente veio da loja
        //Param numero_pedido e token_request_confirmacao_pagamento
        //return http 200 em caso de sucesso
        $numero_pedido                                      =   $_REQUEST['numero_pedido'];

        if(isset($_REQUEST['token_request_confirmacao_pagamento'])){
            $token_request_confirmacao_pagamento_bradesco       =   $_REQUEST['token_request_confirmacao_pagamento'];
        } else {
            $token_request_confirmacao_pagamento_bradesco       =   $_REQUEST['token'];
        }

        $_order                                             =   Mage::getModel('catalog/product')->loadByIncrementId($numero_pedido);
        $token_request_confirmacao_pagamento_loja           =   $_order->getEntityId();

        if($token_request_confirmacao_pagamento_bradesco == $token_request_confirmacao_pagamento_loja){
            $this->getResponse()->setHeader('HTTP/1.0','200',true);
        } else {
            //o token não bateu
            $this->getResponse()->setHeader('HTTP/1.0','300',true);
        }

    }
}
