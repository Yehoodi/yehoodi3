<?php
    class ResourcemanagerController extends CustomControllerAction
    {
        public function init()
        {
            parent::init();
            $this->breadcrumbs->addStep('Account', $this->getUrl(null, 'account'));
            $this->breadcrumbs->addStep('Blog Manager',
                                        $this->getUrl(null, 'blogmanager'));

            $this->identity = Zend_Auth::getInstance()->getIdentity();
        }

        public function imagesAction()
        {
            $request = $this->getRequest();

            $rsrc_id = (int) $request->getPost('id');

            $rsrc = new DatabaseObject_Resource($this->db);
            //if (!$rsrc->loadForUser($this->identity->user_id, $rsrc_id))
            //    $this->_redirect($this->getUrl());

            $json = array();

            if ($request->getPost('upload')) {
                $fp = new FormProcessor_ResourceImage($rsrc);
                if ($fp->process($request))
                    $this->messenger->addMessage('Image uploaded');
                else {
                    foreach ($fp->getErrors() as $error)
                        $this->messenger->addMessage($error);
                }
            }
            else if ($request->getPost('reorder')) {
                $order = $request->getPost('post_images');
                $rsrc->setImageOrder($order);
            }
            else if ($request->getPost('delete')) {
                $image_id = (int) $request->getPost('image');
                $image = new DatabaseObject_BlogPostImage($this->db);
                if ($image->loadForPost($rsrc->getId(), $image_id)) {
                    $image->delete();
                    if ($request->isXmlHttpRequest()) {
                        $json = array(
                            'deleted'  => true,
                            'image_id' => $image_id
                        );
                    }
                    else
                        $this->messenger->addMessage('Image deleted');
                }
            }

            if ($request->isXmlHttpRequest()) {
                $this->sendJson($json);
            }
            else {
                $url = $this->getUrl('preview') . '?id=' . $rsrc->getid();
                $this->_redirect($url);
            }
        }

    }