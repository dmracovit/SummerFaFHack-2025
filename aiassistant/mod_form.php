<?php
require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_aiassistant_mod_form extends moodleform_mod {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('pluginname', 'mod_aiassistant'));

        $mform->addElement('text', 'name', get_string('modulename', 'mod_aiassistant'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        // Topic input
        $mform->addElement('text', 'custom_topic', 'Topic Title');
        $mform->setType('custom_topic', PARAM_TEXT);
        $mform->addRule('custom_topic', null, 'required', null, 'client');

        // Material input
        $mform->addElement('editor', 'custom_material', 'Material Content');
        $mform->setType('custom_material', PARAM_RAW);


        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    public function data_preprocessing(&$default_values) {
    if (!empty($default_values['intro'])) {
        $data = json_decode($default_values['intro'], true);
        if (!empty($data['topic'])) {
            $default_values['custom_topic'] = $data['topic'];
        }
        if (!empty($data['material'])) {
            $default_values['custom_material']['text'] = $data['material'];
            $default_values['custom_material']['format'] = FORMAT_HTML;
        }
    }
}
}
