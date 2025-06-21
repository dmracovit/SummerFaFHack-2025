<?php
// File path: settings.php (NEW FILE)

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_groqchat', get_string('pluginname', 'local_groqchat'));

    // API Configuration
    $settings->add(new admin_setting_heading(
        'local_groqchat/apiheading',
        get_string('apiheading', 'local_groqchat'),
        get_string('apiheading_desc', 'local_groqchat')
    ));

    $settings->add(new admin_setting_configtext(
        'local_groqchat/apikey',
        get_string('apikey', 'local_groqchat'),
        get_string('apikey_desc', 'local_groqchat'),
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_groqchat/apimodel',
        get_string('apimodel', 'local_groqchat'),
        get_string('apimodel_desc', 'local_groqchat'),
        'llama-3.1-8b-instant',
        PARAM_TEXT
    ));

    // AI Balance Training Configuration
    $settings->add(new admin_setting_heading(
        'local_groqchat/trainingheading',
        get_string('trainingheading', 'local_groqchat'),
        get_string('trainingheading_desc', 'local_groqchat')
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_groqchat/enable_level_progression',
        get_string('enable_level_progression', 'local_groqchat'),
        get_string('enable_level_progression_desc', 'local_groqchat'),
        1
    ));

    $settings->add(new admin_setting_configtext(
        'local_groqchat/min_challenges_for_progression',
        get_string('min_challenges_for_progression', 'local_groqchat'),
        get_string('min_challenges_for_progression_desc', 'local_groqchat'),
        '3',
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtext(
        'local_groqchat/independence_threshold_level2',
        get_string('independence_threshold_level2', 'local_groqchat'),
        get_string('independence_threshold_level2_desc', 'local_groqchat'),
        '0.2',
        PARAM_FLOAT
    ));

    $settings->add(new admin_setting_configtext(
        'local_groqchat/independence_threshold_level3',
        get_string('independence_threshold_level3', 'local_groqchat'),
        get_string('independence_threshold_level3_desc', 'local_groqchat'),
        '0.4',
        PARAM_FLOAT
    ));

    $settings->add(new admin_setting_configtext(
        'local_groqchat/independence_threshold_level4',
        get_string('independence_threshold_level4', 'local_groqchat'),
        get_string('independence_threshold_level4_desc', 'local_groqchat'),
        '0.6',
        PARAM_FLOAT
    ));

    $settings->add(new admin_setting_configtext(
        'local_groqchat/independence_threshold_level5',
        get_string('independence_threshold_level5', 'local_groqchat'),
        get_string('independence_threshold_level5_desc', 'local_groqchat'),
        '0.8',
        PARAM_FLOAT
    ));

    // Logging Configuration
    $settings->add(new admin_setting_heading(
        'local_groqchat/loggingheading',
        get_string('loggingheading', 'local_groqchat'),
        get_string('loggingheading_desc', 'local_groqchat')
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_groqchat/enable_detailed_logging',
        get_string('enable_detailed_logging', 'local_groqchat'),
        get_string('enable_detailed_logging_desc', 'local_groqchat'),
        1
    ));

    $settings->add(new admin_setting_configtext(
        'local_groqchat/log_retention_days',
        get_string('log_retention_days', 'local_groqchat'),
        get_string('log_retention_days_desc', 'local_groqchat'),
        '365',
        PARAM_INT
    ));

    $ADMIN->add('localplugins', $settings);
}