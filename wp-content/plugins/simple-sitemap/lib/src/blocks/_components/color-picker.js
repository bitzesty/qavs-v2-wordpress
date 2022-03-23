const { ColorPicker, Dropdown } = wp.components;

export const ColorDropdown = ( props ) => {
	const { color, updateColor, label } = props;

	return (
		<Dropdown
			className="series-color"
			contentClassName="series-color-picker"
			position="bottom left"
			renderToggle={ ( { isOpen, onToggle } ) => (
				<div
					title={ label }
					style={ { backgroundColor: color, cursor: 'pointer' } }
					onClick={ onToggle }
					aria-expanded={ isOpen }
				></div>
			) }
			renderContent={ () => (
				<ColorPicker
					color={ color }
					onChangeComplete={ ( newCol ) => updateColor( newCol ) }
					disableAlpha
				/>
			) }
		/>
	);
};
