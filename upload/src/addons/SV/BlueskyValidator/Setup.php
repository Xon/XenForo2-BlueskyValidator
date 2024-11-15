<?php

namespace SV\BlueskyValidator;

use SV\BlueskyValidator\Validator\Bluesky as BlueskyValidator;
use SV\StandardLib\Helper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Entity\UserField as UserFieldEntity;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

	public function installStep1(): void
	{
		// Add our custom user fields, but only if they don't already exist.
		// Check each individually in case uninstall and reinstall is done to reset a list
        $field = $this->addCustomField('bluesky', 'Bluesky', '');
        $field->match_type = 'validator';
        $field->match_params = ['validator' => BlueskyValidator::class];
        $field->save();
	}

	public function uninstallStep1(): void
	{
		// Leaving the custom user fields in place intentionally, so no uninstall changes needed at this time
	}

	protected function addCustomField(string $name, string $title, string $description): UserFieldEntity
	{
		$field = Helper::find(UserFieldEntity::class, $name);
		if ($field === null)
		{
			/** @var UserFieldEntity $field */
			$field = Helper::createEntity(UserFieldEntity::class);
			$field->field_id = $name;
			$field->display_group = 'contact';
			$field->field_type = 'textbox';
			$field->moderator_editable = true;
			$field->required = false;
			$field->show_registration = false;
			$field->user_editable = 'yes';
			$field->viewable_message = false;
			$field->viewable_profile = true;
			$field->display_order = 10 + (int)\XF::db()->fetchOne('select max(display_order) from xf_user_field');

			// Need a new phrase for the title
			$timeTitle = $field->getMasterPhrase(true);
			$timeTitle->phrase_text = $title;
			$field->addCascadedSave($timeTitle);

			// And another for the description
			$timeDesc = $field->getMasterPhrase(false);
			$timeDesc->phrase_text = $description;
			$field->addCascadedSave($timeDesc);
		}

		return $field;
	}
}
