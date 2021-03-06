<?php
/**
 * FormInputs Controller
 *
 * FormInputs are literally database fields. The goal of this controller is 
 * to allow end user editing of the fields in the database for the main purpose
 * of being able to generate custom forms and extend the data that core methods collect.
 *
 * PHP versions 5
 *
 * Zuha(tm) : Business Management Applications (http://zuha.com)
 * Copyright 2009-2012, Zuha Foundation Inc. (http://zuha.org)
 *
 * Licensed under GPL v3 License
 * Must retain the above copyright notice and release modifications publicly.
 *
 * @copyright     Copyright 2009-2012, Zuha Foundation Inc. (http://zuha.com)
 * @link          http://zuha.com Zuha� Project
 * @package       zuha
 * @subpackage    zuha.app.plugins.forms.controllers
 * @since         Zuha(tm) v 0.0.1
 * @license       GPL v3 License (http://www.gnu.org/licenses/gpl.html) and Future Versions
 * @todo		  Match up the fields, and field options with the full run of cakephp form input options. 
 */
class FormInputsController extends FormsAppController {

	public $name = 'FormInputs';
	
	public $uses = 'Forms.FormInput';


/**
 * Gives us filtered and paginated results for all formInputs. 
 * 
 */
	public function index() {
		$this->FormInput->recursive = 0;		
		$this->set('formInputs', $this->paginate());
	}


/**
 * This function is for adding formInput fields.
 * 
 * @param {id}		The id of the formInput to edit
 * @todo 			formInputs need all the form options that cakephp has, so that you can easily make a database driven form
 * @todo			Move the second part of this save to the model, so that it is simply part of the save operation, and can be reused.
 */
	public function add($fieldsetId = null) {
		if (!empty($this->request->data['FormInput'])) {
			// create the formInput
			try {
				$this->Form->FormInput->add($this->request->data);
				$this->Session->setFlash(__('Input Successfully Added!'));
				$this->redirect(array('controller' => 'forms', 'action' => 'edit', $this->request->data['FormInput']['form_id']));
			} catch (Exception $e) {
				$this->Session->setFlash($e->getMessage());
			}
		}
		
		// variables needed for display of the view
		if ( $fieldsetId === null ) {
			$params = null;
		} else {
			$params = array('conditions'=>array('FormFieldset.id' => $fieldsetId));
		}
		$formFieldsets = $this->FormInput->FormFieldset->find('list', $params);
		
		$this->set(compact('formFieldsets'));
		$this->set('inputTypes', $this->FormInput->inputTypes());
		$this->set('systemDefaultValues', $this->FormInput->systemDefaultValues());
	}
	
/**
 * @todo Need to check to see if this user is authorized to be adding inputs to this Form.id
 * @param type $formId
 */
	public function create($formId) {
		if ( !empty($this->request->data['FormInput']) ) {
			// create the formInput
			try {
				foreach ( $this->request->data['FormInput'] as $formInput ) {
					$this->FormInput->add($formInput);
				}
				$this->Session->setFlash(__('Inputs Successfully Added!'));
			} catch (Exception $e) {
				$this->Session->setFlash($e->getMessage());
			}
			$this->redirect(array('controller' => 'forms', 'action' => 'edit', $formId));
		}

		//$forms = $this->FormInput->Form->find('first', array('conditions' => array('Form.id' => $formId)));

		$this->set(compact('formId'));
		$this->set('inputTypes', $this->FormInput->inputTypes());
		$this->set('systemDefaultValues', $this->FormInput->systemDefaultValues());
	}

/**
 * This function is for editing formInput fields.
 * 
 * @param {id}		The id of the formInput to edit
 * @todo 			formInputs need all the form options that cakephp has, so that you can easily make a database driven form
 * @todo			Changing a field type is NOT working
 * @todo			Overall this function is just too "fat" it needs to be trimmed down, and make the model fat instead.
 * @todo			Move the second part of this save to the model, so that it is simply part of the save operation, and can be reused.
 */
	public function edit($id = null) {
		if (!empty($this->request->data)) {
			// create the formInput
			if($this->FormInput->save($this->request->data)) {
				$this->Session->setFlash(__('Input Successfully Edited!', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->set('duplicate', true);
			}
		}
		
		// variables needed for display of the view
		$this->request->data = $this->FormInput->read(null, $id);
		$formFieldsets = $this->FormInput->FormFieldset->find('list');
		$this->set(compact('formFieldsets'));
		$this->set('inputTypes', $this->FormInput->inputTypes());
		$this->set('systemDefaultValues', $this->FormInput->systemDefaultValues());
	}

/**
 * Delete method
 * 
 * @param uuid
 */
	public function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Form Input', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->FormInput->remove($id)) {
			$this->Session->setFlash(__('Form Input deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
