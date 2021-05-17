/**
 * The Settings component for the Genesis Blocks settings page.
 *
 * Gets and sets data via the genesis-blocks/global-settings data store.
 *
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { compose } from '@wordpress/compose';
import { TabPanel, SlotFillProvider, Slot } from '@wordpress/components';
import { dispatch, select, withSelect } from '@wordpress/data';
import { PluginArea } from '@wordpress/plugins';

/**
 * Internal dependencies
 */
import '../store';
import { SaveButton } from './save-button';
import { Checkbox, Html, Image, Radio, Select, Text, Textarea } from './fields';

// Field types mapped to components. Allows dynamic creation of
// components from section names.
const components = {
	checkbox: Checkbox,
	html: Html,
	image: Image,
	radio: Radio,
	select: Select,
	text: Text,
	textarea: Textarea,
};

/**
 * SettingsComponent
 *
 * @param {Object} { settings, sections }
 * @return {SettingsComponent} The main component of the settings app.
 */
function SettingsComponent({ settings, sections }) {
	/**
	 * Reset the form save state to clear “settings saved“ messages.
	 */
	function resetFormSaveState() {
		dispatch('genesis-blocks/global-settings').resetFormSaveState();
	}

	/**
	 * Renders fields for a named section.
	 *
	 * @param {string} section The section name to load fields for.
	 */
	function renderFields(section) {
		if (section.hasOwnProperty('fields') && Array.isArray(section.fields)) {
			const fields = section.fields.map(function (field, index) {
				if (!components.hasOwnProperty(field.type)) {
					return '';
				}
				const Field = components[field.type];
				return <Field key={index} settings={settings} field={field} />;
			});

			if (fields.length > 0) {
				return <>{fields}</>;
			}
		}

		return <p>{__('No fields found for this section.')}</p>;
	}

	return (
		<>
			<TabPanel
				className="genesis-blocks-settings-sections"
				activeClass="is-active"
				onSelect={resetFormSaveState}
				tabs={Object.values(sections)}
			>
				{(tab) => (
					<>
						{renderFields(tab)}
						<SlotFillProvider>
							<Slot name={"GenesisBlocksSettings_" + tab.name.replace('genesis_blocks_settings_', '')} />
							<PluginArea />
						</SlotFillProvider>
						<SaveButton
							successMessage={__('Settings saved')}
							failMessage={__('Saving failed')}
							messageDuration="2"
						>
							{__('Save All')}
						</SaveButton>
					</>
				)}
			</TabPanel>
		</>
	);
}

export const Settings = compose([
	// Subscribe to changes to the settings and sections state.
	withSelect(() => {
		return {
			settings: select('genesis-blocks/global-settings').getSettings(),
			sections: select('genesis-blocks/global-settings').getSections(),
		};
	}),
])(SettingsComponent);
