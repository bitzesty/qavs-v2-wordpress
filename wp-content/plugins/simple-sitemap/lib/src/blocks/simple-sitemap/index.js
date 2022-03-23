import Select from 'react-select';
import { SitemapCheckboxControl } from '../_components/checkbox';
import { ServerSideRenderX } from '../_components/server-side-render-x';

//  Import core block libraries
const { __ } = wp.i18n;
const { InspectorControls } = wp.blockEditor;
const {
	PanelBody,
	PanelRow,
	TextControl,
	//RadioControl,
	SelectControl,
	//ServerSideRender
} = wp.components;
const { registerBlockType } = wp.blocks;
const {
	//Fragment
} = wp.element;
const ServerSideRender = wp.serverSideRender;
const { applyFilters, addAction, addFilter } = wp.hooks;

/**
 * Register block
 */
export default registerBlockType( 'wpgoplugins/simple-sitemap-block', {
	title: 'Simple Sitemap',
	icon: 'editor-ul',
	keywords: [ 
		__( 'Sitemap', 'simple-sitemap' ), 
		__( 'Single', 'simple-sitemap' ), 
		__( 'HTML Sitemap', 'simple-sitemap' ) 
	],
	category: 'simple-sitemap',
	example: {
		attributes: {
				num_posts: 20,
		},
	},
	edit: ( props ) => {
		const {
			attributes: {
				show_excerpt,
				show_label,
				links,
				page_depth,
				nofollow,
				image,
				list_icon,
				max_width,
				responsive_breakpoint,
				sitemap_container_margin,
				sitemap_item_line_height,
				tab_color,
				tab_header_bg,
				post_type_label_padding,
				post_type_label_font_size,
				render_tab,
				block_post_types,
				exclude,
				include,
				order,
				orderby,
			},
			//className,
			setAttributes,
			//isSelected,
			attributes,
		} = props;

		//const defaultValue = JSON.parse(attributes.block_post_types);
		//console.log(defaultValue);
		//console.log(typeof attributes.block_post_types, attributes.block_post_types);
		//console.log(JSON.parse(attributes.block_post_types));

		function updateToggleTabs( isChecked ) {
			setAttributes( { render_tab: isChecked } );
		}

		function updateExcerpt( isChecked ) {
			setAttributes( { show_excerpt: isChecked } );
		}

		function updateShowLabel( isChecked ) {
			setAttributes( { show_label: isChecked } );
		}

		function updateLinks( isChecked ) {
			setAttributes( { links: isChecked } );
		}

		const selectStyles = {
			container: ( styles ) => ( {
				...styles,
				marginBottom: "5px",
				'& div[class$="-Input"]': {
					'& input:focus': {
						boxShadow: 'none',
					},
				},
			} ),
		};

		const selectPostTypes = applyFilters(
			'sitemap-post-types-select',
			<Select
				title="Title"
				defaultValue={ JSON.parse( block_post_types ) }
				isMulti
				onChange={ ( value ) => {
					return setAttributes( {
						block_post_types: JSON.stringify( value ),
					} );
				} }
				options={ [
					{ value: 'post', label: 'Post' },
					{ value: 'page', label: 'Page' },
				] }
				styles={ selectStyles }
			/>,
			props
		);

		const postTypesHelpLabel = applyFilters(
			'post-types-help-label',
			<PanelRow className="panel-row-help-label">
				<p
					style={ {
						marginTop: '-20px',
						fontSize: '13px',
						fontStyle: 'italic',
						marginLeft: '2px',
					} }
				>
					List{ ' ' }
					<a
						href="https://wpgoplugins.com/plugins/simple-sitemap-pro/#post-types"
						target="_blank"
						rel="noreferrer"
					>
						more
					</a>{ ' ' }
					post types
				</p>
			</PanelRow>,
			props
		);

		const sitemapGeneralSettings = applyFilters( 'sitemap-general-settings', '', props );
		const sitemapGeneralStyles = applyFilters( 'sitemap-general-styles', '', props );
		const sitemapFeaturedImage = applyFilters( 'sitemap-featured-image', '', props );
		const sitemapTabControls = applyFilters( 'sitemap-tab-controls', '', props );

		return [
			<InspectorControls key="simple-sitemap-block-controls">
				<PanelBody title={ __( 'General Settings', 'simple-sitemap' ) }>
					<PanelRow className="panel-row-label">
						<label
							style={ { marginBottom: '-14px' } }
							className="components-base-control__label"
						>
							Select post types to display
						</label>
					</PanelRow>
					<PanelRow>
						{selectPostTypes}
					</PanelRow>
					{postTypesHelpLabel}
					<PanelRow className="simple-sitemap order">
						<SelectControl
							label="Orderby"
							value={ orderby }
							options={ [
								{ label: 'Title', value: 'title' },
								{ label: 'Date', value: 'date' },
								{ label: 'ID', value: 'ID' },
								{ label: 'Author', value: 'author' },
								{ label: 'Name', value: 'name' },
								{ label: 'Modified', value: 'modified' },
								{ label: 'Menu Order', value: 'menu_order' },
								{ label: 'Random Order', value: 'rand' },
								{ label: 'Comment Count', value: 'comment_count' },
							] }
							onChange={ ( value ) => {
								setAttributes( { orderby: value } );
							} }
						/>
						<SelectControl
							label="Order"
							value={ order }
							options={ [
								{ label: 'Ascending', value: 'asc' },
								{ label: 'Descending', value: 'desc' },
							] }
							onChange={ ( value ) => {
								setAttributes( { order: value } );
							} }
						/>
					</PanelRow>
					<PanelRow className="simple-sitemap general-chk">
						<SitemapCheckboxControl
							value={ show_label }
							label="Show post type label"
							updateCheckbox={ updateShowLabel }
						/>
					</PanelRow>
					<PanelRow className="simple-sitemap general-chk">
						<SitemapCheckboxControl
							value={ show_excerpt }
							label="Show excerpt"
							updateCheckbox={ updateExcerpt }
						/>
					</PanelRow>
					<PanelRow className="simple-sitemap general-chk">
						<SitemapCheckboxControl
							value={ links }
							label="Enable sitemap links"
							updateCheckbox={ updateLinks }
						/>
					</PanelRow>
					{ sitemapGeneralSettings }
				</PanelBody>
				{ sitemapGeneralStyles }
				{ sitemapFeaturedImage }
				<PanelBody
					title={ __( 'Tab Settings', 'simple-sitemap' ) }
					initialOpen={ false }
				>
					<PanelRow className="simple-sitemap">
						<SitemapCheckboxControl
							value={ render_tab }
							label="Enable tabs"
							updateCheckbox={ updateToggleTabs }
						/>
					</PanelRow>
					{ sitemapTabControls }
				</PanelBody>
				<PanelBody
					title={ __( 'Page Settings', 'simple-sitemap' ) }
					initialOpen={ false }
				>
					<PanelRow className="simple-sitemap">
						<p>Affects sitemap pages only.</p>
					</PanelRow>
					<PanelRow className="simple-sitemap">
						<TextControl
							type="number"
							label="Page indentation"
							min="0"
							max="5"
							help="Leave at zero for auto-depth"
							value={ page_depth }
							onChange={ ( value ) => {
								setAttributes( {
									page_depth: parseInt( value ),
								} );
							} }
						/>
					</PanelRow>
				</PanelBody>
			</InspectorControls>,
			<ServerSideRenderX
				key="simple-sitemap-server-side-render-component"
				block="wpgoplugins/simple-sitemap-block"
				attributes={attributes}
			/>,
			// <ServerSideRender
			// 	key="simple-sitemap-server-side-render-component"
			// 	block="wpgoplugins/simple-sitemap-block"
			// 	attributes={ attributes }
			// />,
		];
	},
	save() {
		return null;
	},
} );
