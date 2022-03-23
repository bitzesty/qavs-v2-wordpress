const { CheckboxControl } = wp.components;

export const SitemapCheckboxControl = ( props ) => {
	const { help, disabled, label, updateCheckbox, value } = props;

	return (
		<CheckboxControl
			help={ help }
			disabled={ disabled }
			label={ label }
			checked={ value }
			onChange={ ( isChecked ) => updateCheckbox( isChecked ) }
		/>
	);
};
