/**
 * Extend blocks with pro features.
 */

import { SelectCPT } from './_components/select-cpt';
import { SitemapCheckboxControl } from './_components/checkbox';
import { ColorDropdown } from './_components/color-picker.js';
const { applyFilters, addAction, addFilter } = wp.hooks;
const {
	// 	Button,
	// 	ButtonGroup,
	// 	Modal,
	TextControl,
	// 	TextareaControl,
	// 	CheckboxControl,
	SelectControl,
	PanelBody,
	PanelRow,
	// 	ToggleControl,
} = wp.components;
const { Component, Fragment } = wp.element;
const { __ } = wp.i18n;
// eslint-disable-next-line no-undef
const simpleSitemapCanUsePremiumCode = simple_sitemap_editor_data.can_use_premium_code;

// import { BorderRadius } from '../blocks/__components/border-radius';
// import { NumberRangesFour } from '../blocks/__components/number-ranges-four';
// import { NumberRangesFourCompact } from '../blocks/__components/number-ranges-four-compact';
// import { setStyle, updateFaqItems } from '../blocks/__components/utility';
// import ReactMultiColorPicker from '../blocks/__components/color-pickers/react-multi-color-picker';
// import { Debug, getThemes } from '../blocks/__components/utility';
// import { BorderInputControl } from '../blocks/__components/border/border';

/**
 * Extend SelectCptTaxonomy component to include post types drop down.
 */

// set the CPT dropdown options
function fetchCPTs( props ) {
	const { block_post_type, block_taxonomy } = props;
	const post_type_url = 'simple-sitemap/v1/post-types';

	return wp.apiFetch( { path: post_type_url, method: 'GET' } ).then(
		( data ) => {
			const post_types = [];
			const entries = Object.entries( data );
			for ( const [ key, value ] of entries ) {
				const tmp = {
					value: key,
					label: value,
				};
				post_types.push( tmp );
			}

			return post_types;
		},
		( err ) => {
			return err;
		}
	);
}

addFilter( 'fetch-sitemap-cpts', 'simple-sitemap-filter', function ( fn ) {
	return fetchCPTs;
} );

addFilter(
	'post-types-help-label',
	'simple-sitemap-filter',
	function ( component ) {
		return '';
	}
);

addFilter(
	'sitemap-post-types-select',
	'simple-sitemap-filter',
	function ( component, props ) {
		const {
			attributes: { block_post_types },
			setAttributes,
		} = props;

		return (
			<SelectCPT
				setAttributes={setAttributes}
				block_post_types={block_post_types}
			/>
		);
	}
);

addFilter(
	'sitemap-group-post-types-select',
	'simple-sitemap-filter',
	function ( component, props, state, updatePostTypeValues ) {
		const { block_post_type } = props;

		//console.log('Q HOOK:', q_padding, component, atts, setAttributes);

		return (
			<SelectControl
				label="Select post type"
				value={ block_post_type }
				options={ state.types }
				onChange={updatePostTypeValues}
				help={ state.taxonomy_select_disabled_help }
			/>
		);
	}
);

addFilter(
	'sitemap-list-more-taxonomies',
	'simple-sitemap-filter',
	function ( component ) {
		return '';
	}
);

addFilter(
	'sitemap-general-settings',
	'simple-sitemap-filter',
	function ( component, props ) {
		const {
			attributes: { exclude, include, visibility, horizontal, nofollow, list_icon },
			setAttributes,
		} = props;

		//console.log('FILTER: ', props);

		return (
			<Fragment>
				<PanelRow className="simple-sitemap general-chk">
					<SitemapCheckboxControl
						value={ list_icon }
						label="Display list icons"
						updateCheckbox={ ( isChecked ) => {
							setAttributes( { list_icon: isChecked } );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap general-chk">
					<SitemapCheckboxControl
						value={ visibility }
						label="Display private posts"
						updateCheckbox={ ( isChecked ) => {
							setAttributes( { visibility: isChecked } );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap general-chk">
					<SitemapCheckboxControl
						value={ horizontal }
						label="Enable horizontal sitemap"
						updateCheckbox={ ( isChecked ) => {
							setAttributes( { horizontal: isChecked } );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap general-chk">
					<SitemapCheckboxControl
						value={ nofollow }
						label="Nofollow links"
						updateCheckbox={ ( isChecked ) => {
							setAttributes( { nofollow: isChecked } );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap">
					<TextControl
						label="Exclude post IDs from sitemap"
						help="Enter comma separated list of post ID's"
						value={ exclude }
						onChange={ ( value ) => {
							setAttributes( { exclude: value } );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap">
					<TextControl
						label="Include post IDs in sitemap"
						help="Enter comma separated list of post ID's"
						value={ include }
						onChange={ ( value ) => {
							setAttributes( { include: value } );
						} }
					/>
				</PanelRow>
			</Fragment>
		);
	}
);

addFilter(
	'sitemap-group-general-settings',
	'simple-sitemap-filter',
	function ( component, props ) {
		const {
			attributes: { exclude, include, visibility, horizontal, nofollow, list_icon },
			setAttributes,
		} = props;

		//console.log('FILTER: ', props);

		return (
			<Fragment>
				<PanelRow className="simple-sitemap general-chk">
					<SitemapCheckboxControl
						value={ list_icon }
						label="Display list icons"
						updateCheckbox={ ( isChecked ) => {
							setAttributes( { list_icon: isChecked } );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap general-chk">
					<SitemapCheckboxControl
						value={ visibility }
						label="Display private posts"
						updateCheckbox={ ( isChecked ) => {
							setAttributes( { visibility: isChecked } );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap general-chk">
					<SitemapCheckboxControl
						value={ nofollow }
						label="Nofollow links"
						updateCheckbox={ ( isChecked ) => {
							setAttributes( { nofollow: isChecked } );
						} }
					/>
				</PanelRow>
				{/* <PanelRow className="simple-sitemap">
					<TextControl
						label="Exclude posts"
						help="Enter comma separated list of post ID's"
						value={ exclude }
						onChange={ ( value ) => {
							setAttributes( { exclude: value } );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap">
					<TextControl
						label="Include posts"
						help="Enter comma separated list of post ID's"
						value={ include }
						onChange={ ( value ) => {
							setAttributes( { include: value } );
						} }
					/>
				</PanelRow> */}
			</Fragment>
		);
	}
);

addFilter(
	'sitemap-general-styles',
	'simple-sitemap-filter',
	function ( component, props ) {
		const {
			attributes: {
				post_type_label_font_size,
				sitemap_container_margin,
				sitemap_item_line_height },
			setAttributes,
		} = props;

		//console.log('FILTER: ', props);

		return (
			<PanelBody
				title={ __( 'Sitemap Styles', 'simple-sitemap' ) }
				initialOpen={ false }
			>
				<PanelRow className="simple-sitemap">
					<TextControl
						label="Post Type Font size"
						placeholder="e.g. 1em or 12px"
						help="Leave blank to use theme styles"
						value={ post_type_label_font_size }
						onChange={ ( value ) => {
							setAttributes( {
								post_type_label_font_size: value,
							} );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap">
					<TextControl
						label="Sitemap container margin"
						placeholder="e.g. 0 0 0 2em"
						help="Leave blank to use defaults"
						value={sitemap_container_margin}
						onChange={ ( value ) => {
							setAttributes( {
								sitemap_container_margin: value,
							} );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap">
					<TextControl
						label="Sitemap item line height"
						placeholder="e.g. 1em or 12px"
						help="Leave blank to use theme styles"
						value={ sitemap_item_line_height }
						onChange={ ( value ) => {
							setAttributes( {
								sitemap_item_line_height: value,
							} );
						} }
					/>
				</PanelRow>
			</PanelBody>
		);
	}
);

addFilter(
	'sitemap-group-general-styles',
	'simple-sitemap-filter',
	function ( component, props ) {
		const {
			attributes: {
				post_type_label_font_size,
				sitemap_container_margin,
				sitemap_item_line_height },
			setAttributes,
		} = props;

		//console.log('FILTER: ', props);

		return (
			<PanelBody
				title={ __( 'Sitemap Styles', 'simple-sitemap' ) }
				initialOpen={ false }
			>
				<PanelRow className="simple-sitemap">
					<TextControl
						label="Post Type Font size"
						placeholder="e.g. 1em or 12px"
						help="Leave blank to use theme styles"
						value={ post_type_label_font_size }
						onChange={ ( value ) => {
							setAttributes( {
								post_type_label_font_size: value,
							} );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap">
					<TextControl
						label="Sitemap container margin"
						placeholder="e.g. 0 0 0 2em"
						help="Leave blank to use defaults"
						value={sitemap_container_margin}
						onChange={ ( value ) => {
							setAttributes( {
								sitemap_container_margin: value,
							} );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap">
					<TextControl
						label="Sitemap item line height"
						placeholder="e.g. 1em or 12px"
						help="Leave blank to use theme styles"
						value={ sitemap_item_line_height }
						onChange={ ( value ) => {
							setAttributes( {
								sitemap_item_line_height: value,
							} );
						} }
					/>
				</PanelRow>
			</PanelBody>
		);
	}
);

addFilter(
	'sitemap-featured-image',
	'simple-sitemap-filter',
	function ( component, props ) {
		const {
			attributes: { image },
			setAttributes,
		} = props;

		//console.log('FILTER: ', props);

		return (
			<PanelBody
				title={ __( 'Featured Image Settings', 'simple-sitemap' ) }
				initialOpen={ false }
			>
				<PanelRow className="simple-sitemap">
					<SitemapCheckboxControl
						value={ image }
						label="Display featured images"
						updateCheckbox={ ( isChecked ) => {
							setAttributes( { image: isChecked } );
						} }
					/>
				</PanelRow>
			</PanelBody>
		);
	}
);

addFilter(
	'sitemap-group-featured-image',
	'simple-sitemap-filter',
	function ( component, props ) {
		const {
			attributes: { image },
			setAttributes,
		} = props;

		//console.log('FILTER: ', props);

		return (
			<PanelBody
				title={ __( 'Featured Image Settings', 'simple-sitemap' ) }
				initialOpen={ false }
			>
				<PanelRow className="simple-sitemap">
					<SitemapCheckboxControl
						value={ image }
						label="Display featured images"
						updateCheckbox={ ( isChecked ) => {
							setAttributes( { image: isChecked } );
						} }
					/>
				</PanelRow>
			</PanelBody>
		);
	}
);

addFilter(
	'sitemap-tab-controls',
	'simple-sitemap-filter',
	function ( component, props ) {
		const {
			attributes: {
				render_tab,
				tab_header_bg,
				tab_color,
				post_type_label_padding,
				responsive_breakpoint,
				max_width
			},
			setAttributes,
		} = props;

		//console.log('FILTER: ', props);

		return render_tab ? (
			<Fragment>
				<PanelRow className="tab-colors simple-sitemap">
					<h4 style={ { marginBottom: '10px' } }>Tab colors</h4>
					<ColorDropdown
						label="Active tab background"
						color={ tab_header_bg }
						updateColor={ ( newCol ) => {
							setAttributes( { tab_header_bg: newCol.hex } );
						} }
					/>
					<ColorDropdown
						label="Active tab text"
						color={ tab_color }
						updateColor={ ( newCol ) => {
							setAttributes( { tab_color: newCol.hex } );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap">
					<TextControl
						label="Tab header padding"
						placeholder="e.g. 10px 40px"
						value={ post_type_label_padding }
						onChange={ ( value ) => {
							setAttributes( { post_type_label_padding: value } );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap">
					<TextControl
						label="Responsive breakpoint"
						placeholder="e.g. 500px"
						help="Width that responsive styles are enabled"
						value={ responsive_breakpoint }
						onChange={ ( value ) => {
							setAttributes( { responsive_breakpoint: value } );
						} }
					/>
				</PanelRow>
				<PanelRow className="simple-sitemap">
					<TextControl
						label="Maximum width"
						placeholder="e.g. 500px"
						help="Leave blank for no max. width"
						value={ max_width }
						onChange={ ( value ) => {
							setAttributes( { max_width: value } );
						} }
					/>
				</PanelRow>
			</Fragment>
		) : (
			''
		);
	}
);
