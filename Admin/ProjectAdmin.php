<?php

namespace IMOControl\M3\ProjectBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Validator\ErrorElement;

use IMOControl\M3\AdminBundle\Admin\Admin as CoreAdmin;

class ProjectAdmin extends CoreAdmin
{
	protected $baseRouteName = 'imoc_admin_project';
	protected $baseRoutePattern = '/projectmanager/project';
	
    /**
	 * 
	 */
    public function prePersist($object) {
        $object->setCreatedAt(new \DateTime());
        
        if (is_null($object->getAlias()) || $object->getAlias() == '') {
            $object->setAlias(null);
        }
		
		if ($object->getProjectAddressStreet() == '') {
			// Load address data from customer
			$address = $object->getCustomer()->getAddress();
			$object->setProjectAddressStreet($address->getStreet());
			$object->setProjectAddressPlz($address->getPostalcode());
			$object->setProjectAddressCity($address->getCity());
			$object->setProjectAddressCountry($address->getCountry());
			$this->getRequest()->getSession()->setFlash('m3_flash_info', 'Kundenadresse wurde erfolgreich als Projetsadresse übernommen.');
		}
    }
    
	/**
	 * {@inheritdoc}
	 */
    public function preUpdate($object) {
        $object->setUpdatedAt(new \DateTime());
    }
 	
	/**
	 * {@inheritdoc}
	 */
    public function postPersist($object)
    {
        /*$report = $object->getAuthorityReport();
		if (is_object($report)) {
			$report->setProject($object);
        	$this->getModelManager()->update($report);
		}
        // */
        
        if (is_null($object->getAlias())) {
            $object->setAlias($object->getId() . '-' . date('Y', $object->getCreatedAt()->getTimestamp()));
            $this->getModelManager()->update($object);
        }
        
        $this->__initProjectArchiv($object);
        
        // Check email from customer
        /*if ($object->getSendCustomer() == 1) {
            if ($object->getCustomer()->getEmail() == '') {
                $this->getRequest()->getSession()->setFlash('m3_flash_warning', 'Achtung noch keine Emailadresse bei dem Kunden hinterlegt an welche die Dokumente zugestellt werden können!');
            }
        } else {
            // Check post address
            if (is_object($object->getCustomer()) && $object->getCustomer()->getAddress() == null) {
                $this->getRequest()->getSession()->setFlash('m3_flash_warning', 'Achtung noch keine Rechnungsadresse für den Kunden hinterlegt!');
            
            }
        }
        //*/
    }

    /**
	 * {@inheritdoc}
	 */
    public function postUpdate($object) {
        $this->postPersist($object);
    }
    
    
    public function preRemove($object) {
        
        //$object->setAuthorityReport(null);
        $this->getModelManager()->update($object);
		if (is_dir($object->getProjectPath(true))) {
			rename($object->getProjectPath(true), Project::ROOTPATH . '.GELÖSCHT_'. $object->getAlias() . '-' . $object->getName());
		}
    }
    
    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        //$this->setTemplate('show','::CRUD/show_project.html.twig');
        
        $showMapper
            ->with('General')
                ->add('name')
                ->add('alias')
                ->add('project_path')
                ->add('customer')
            ->end()
        ;
        
        $showMapper
            ->with('Projectaddress')
                ->add('project_address_street')
                ->add('project_address_plz')
                ->add('project_address_city')
                ->add('project_address_country')
            ->end()
        ;
        
        $showMapper
            ->with('Invoiceaddress')
                ->add('customer.salutation')
                ->add('customer.address.street')
                ->add('customer.address.postalcode')
                ->add('customer.address.city')
                ->add('customer.address.country')
            ->end()
        ;
        
        /*$showMapper
            ->with('Authority report')
                ->add('bh')
                ->add('authority_report')
            ->end()
        ;//*/
        
        /*
        $showMapper
            ->with('System selection')
                ->add('cleaning_system', null, array('template' => '::CRUD/show_array_anlage.html.twig'))
            ->end()
        ;
        
        $showMapper
            ->with('Certificates')
                ->add('certificates', null, array())
            ->end()
        ;
        
        $showMapper
            ->with('Contract')
                ->add('contract')
            ->end()
        ;
        // */
        $showMapper
            ->with('Invoices')
                ->add('invoices')
            ->end()
        ;
        
        $showMapper
            ->with('System Information')
                ->add('created_at')
                ->add('updated_at')
            ->end()
        ;
        
            
        
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        //$object = $this->getSubject();
        //echo "<pre>" . print_r(get_class_methods($object->getCertificates()), true) . "</pre>";
        
    	//$bhQuery =  $this->modelManager->getEntityManager('ApplicationM3IngBundle:Authority')->createQuery("SELECT a FROM ApplicationM3IngBundle:Authority a WHERE a.type = 'BH' ORDER BY a.name ASC");
        
        $edit = false;
        $readonly = array();
        if ($this->hasRequest() && $this->getRequest()->get('id')) {
            $edit = true;
            $readonly = array('read_only' => true);
        }
        
        $formMapper
            ->with('General')
				->add('name', null, $readonly)
				->add('alias', null, $readonly)
                //->add('project_path')
                ->add('customer', 'sonata_type_model_list', array('by_reference'=>true))
                //->add('invoice_address', 'sonata_type_model_list', array('by_reference'=>true, 'compound' => true))
                //->add('send_customer', 'choice', array('choices' => array('POST' => 'Post', 'EMAIL' => 'E-Mail')));
                ;
        if ($edit) {
            $formMapper->add('deckblatt')
            
            	->add('contract', 'sonata_type_model_list', 	array('by_reference' => true, 'required' => false),
            														array('link_parameters' => array('project' => $this->getRequest()->get('id'))));
        }    
                
        $formMapper->end();
		
		$formMapper
		    ->with('Projectaddress', array('collapsed' => false))
                ->add('project_address_street', 'text', array('required' => false))
                ->add('project_address_postalcode', 'text', array('required' => false))
                ->add('project_address_city', 'text', array('required' => false))
                ->add('project_address_country', 'country', array('preferred_choices' => array('AT'), 'required' => false))
            ->end()
        ;
        /*
        $formMapper
            ->with('Authority report', array('collapsed' => ($edit) ? true : false))
                ->add('bh', 'sonata_type_model', array('query' => $bhQuery, 'help' => 'current_authority'))
                //->add('authority_report', 'sonata_type_model')
                ->add('authority_report','sonata_type_model_list', array('by_reference'=>true, 'required' => false))
                //->add('bbl', 'sonata_type_model_list', array('by_reference'=>true))
            ->end()
        ;
        
        $formMapper
            ->with('System selection', array('collapsed' => ($edit) ? true : false))
                ->add('anlagen_hersteller', 'genemu_jqueryautocompleter_choice', array(
			            'choices' => array(
			                'SW Umwelttechnik AG, Klagenfurt' => 'SW Umwelttechnik AG, Klagenfurt',
			                'TIBA Austria GmbH, Lebring' => 'TIBA Austria GmbH, Lebring',
			                'n.A.' => 'Nicht angegeben'
			            )))
                ->add('anlagen_type', 'text', array('required' => false))
                ->add('cleaning_system', 'sonata_type_immutable_array', array('keys' => array(
                                                                            //Belebungsanlage
                                                                            array('Tbparam1',   'checkbox',  array('required' => false, 'translation_domain' => 'application')),
                                                                            // Vorklärung
                                                                            array('Tbparam2',   'checkbox',  array('required' => false, 'translation_domain' => 'application')),
                                                                                // einteilig mit Nutzvolumen von ... m3
                                                                                array('Tbparam21', 'number',  array('required' => false, 'error_bubbling' => true, 'invalid_message' => 'Anlagenaufbau: Parameter Nutzvolumen muss aus einer Zahl bestehen.', 'translation_domain' => 'application')),
                                                                                 // zweiteilig
                                                                                array('Tbparam22', 'number',  array('required' => false, 'error_bubbling' => true, 'invalid_message' => 'Anlagenaufbau: Parameter Nutzvolumen muss aus einer Zahl bestehen.', 'translation_domain' => 'application')),
                                                                                // dreiteilig
                                                                                array('Tbparam23', 'number',  array('required' => false, 'error_bubbling' => true, 'invalid_message' => 'Anlagenaufbau: Parameter Nutzvolumen muss aus einer Zahl bestehen.', 'translation_domain' => 'application')),
                                                                            // Feststoffabtrennung mit Bogensieb
                                                                            array('Tbparam24',   'checkbox',  array('required' => false, 'translation_domain' => 'application')),
                                                                            // Kippküebelschacht
                                                                            array('Tbparam25',   'checkbox',  array('required' => false, 'translation_domain' => 'application')),
                                                                            
                                                                            // Naßentschlammung
                                                                            array('Tbparam26',   'checkbox',  array('required' => false, 'translation_domain' => 'application')),
                                                                            // Pufferstuffe
                                                                            array('Tbparam3',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Pflanzenklärbecken
                                                                            array('Tbparam31',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Belebungsstufe im Durchlauf
                                                                            array('Tbparam4',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Belebungsstufe im Aufstau
                                                                            array('Tbparam5',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // SBR Belebungsstufe mit Schlammspeicher
                                                                            array('Tbparam51',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Tropfkörper mit Rezirkulation
                                                                            array('Tbparam6',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Tropfkörper ohne Rezirkulation
                                                                            array('Tbparam62',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
																			// belüftetes Festbett                                                                             
                                                                            array('Tbparam63',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Rotationstauchkörper
                                                                            array('Tbparam7',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Nachklärung
                                                                            array('Tbparam8',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Nachfilter
                                                                            array('Tbparam9',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Pflanzenstuffe als Nachreinigung
                                                                            array('Tbparam91',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Probenahmeschacht
                                                                            array('Tbparam10',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Probenahmeschacht mit Drainageversickerung
                                                                            array('Tbparam101',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // obertägige Verrieselung
                                                                            array('Tbparam11',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // oberflächennahe Verrieselung
                                                                            array('Tbparam12',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Sickerschacht
                                                                            array('Tbparam13',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Vererdungsbecken
                                                                            array('Tbparam131',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Einleitung in Vorfluter
                                                                            array('Tbparam14',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            // Sickermulde
                                                                            array('Tbparam15',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            array('Tbparam20',   'checkbox',  array('required' => false, 'translation_domain' => 'application')), 
                                                                            
                                                                        )))
            ->end()
        ;
        // */
        if ($edit) {
            /*
            $formMapper
                ->with('Certificates', array('collapsed' => true))
                    ->add('certificates', 'sonata_type_model', array('required' => false,
                    												  'multiple' => true,
                    												  'expanded' => true,
                                                                      'by_reference' => true,
                                                                      ))
                                                                
                ->end()
            ;
            // */
            $formMapper
                ->with('Invoices', array('collapsed' => true))
                    ->add('invoices', 'sonata_type_model', array('required' => false,
                    												  'multiple' => true,
                    												  'expanded' => true,
                                                                      'by_reference' => true,
                                                                      ))
                ->end()
            ;
      }
            
		
    }

	public function validate(ErrorElement $errorElement, $object) {
		/*$errorElement
            ->with('customer')
                ->assertNotNull()
				->addViolation('Bitte ausfüllen!')
            ->end()
			->with('authority_report')
                ->assertNotNull(array())
				->addViolation('Bitte ausfüllen!')
            ->end()
			->with('customer')
                ->assertNotNull(array())
				->addViolation('Bitte ausfüllen!')
            ->end()
        ; // */
	}

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
			->addIdentifier('alias')
			->addIdentifier('name')
			->add('customer', null, array(
                        'sortable'=>true, 
                        'sort_field_mapping'=> array('fieldName'=>'firstname'),
                        'sort_parent_association_mappings' => array(array('fieldName'=>'customer')),
                        'sort_order' => 'DESC'
                    ))
            ->add('projectAddress') // View full Project address
            ->add('project_address_postalcode', 'number', array('sortable' => true,'sort_order' => 'DESC'))
            
        ;
        /*
		if (!$this->getRequest()->isXmlHttpRequest()) {   
        $listMapper
            ->add('authority_report.check_type_formated', null, array(
                        'sortable'=>true, 
                        'sort_field_mapping'=> array('fieldName'=>'check_type'),
                        'sort_parent_association_mappings' => array(array('fieldName'=>'authority_report')),
                        'sort_order' => 'DESC'
                    ))
            ->add('authority_report.checkable_to_formated_all', null, array(
                        'sortable'=>true, 
                        'sort_field_mapping'=> array('fieldName'=>'checkable_to'),
                        'sort_parent_association_mappings' => array(array('fieldName'=>'authority_report')),
                        'sort_order' => 'DESC'
                    ))
			->add('nextWaterCheck')
			->add('lastWaterCheck')
            ->add('authority_report')       
        ;
		}
		// */
		$listMapper
            ->add('_action', 'actions', array(
                'actions' => array(
                   'view' => array('template' => '::M3/actions/list__action_view.html.twig'),
                    'edit' => array('template' => '::M3/actions/list__action_edit.html.twig'),
                )
            ))
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        	->add('name')
        	//->add('name', null, array(), 'genemu_jqueryautocompleter_entity', array('class' => 'ApplicationM3IngBundle:Project', 'property' => 'name'))
        	->add('alias')
        	//->add('alias', null, array(), 'genemu_jqueryautocompleter_entity', array('class' => 'ApplicationM3IngBundle:Project', 'property' => 'alias'))
        	->add('customer',  null, array(), 'genemu_jqueryautocompleter_entity')
        	//->add('certificates',  null, array(), 'genemu_jqueryautocompleter_entity')
            ->add('project_address_city')
            ->add('project_address_plz')
            ->add('project_address_street')
            //->add('authority_report.check_type', null, array(), 'choice', array('choices' => array(1 => 'Jährlich', 2 => 'Halbjährlich', 12 => 'Monatlich')))
            //->add('authority_report.checkable_to', null, array(), 'choice', array('choices' => range(0,12,1)))
            //->add('agegroups', null, array('field_options' => array('multiple' => true)))
            //->add('bowgroups', null, array('field_options' => array('multiple' => true)))
        ;
    }

    
    protected function __initProjectArchiv($object) {
        
        if ($object->getProjectPath() == '' || !is_dir($object->getProjectPath(true))) {
            
            // Replace whitespace with _ underscore 
            $path = str_replace(' ', '_', sprintf("%s_%s_%s", $object->getAlias(), $object->getName(), $object->getProjectAddressCity()));
			$path = str_replace('/', '-', $path) . "/";
			$path = str_replace('__', '_', $path);
            $object->setProjectPath($path);
            $this->getModelManager()->update($object);
            
            mkdir($object->getProjectPath(true), 0775, true);
            mkdir($object->getProjectPath(true) . "Uploads/", 0775);
            mkdir($object->getProjectPath(true) . "Rechnungen/", 0775);
            mkdir($object->getProjectPath(true) . "Gutachten/", 0775);
            
            $this->getRequest()->getSession()->setFlash('m3_flash_info', 'Neuer Projektordner ['. $path . '] sowie alle Unterordner wurden erstellt.');
            
        }
    }
}