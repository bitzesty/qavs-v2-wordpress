import { SelectCptTaxonomy } from '../_components/select-cpt-taxonomy';
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
	//ColorPicker
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
export default registerBlockType( 'wpgoplugins/simple-sitemap-group-block', {
	title: 'Simple Sitemap Group',
	icon: 'networking',
	keywords: [ 
		__( 'Sitemap', 'simple-sitemap' ), 
		__( 'Group', 'simple-sitemap' ), 
		__( 'HTML Sitemap', 'simple-sitemap' ) 
	],
	category: 'simple-sitemap',
	example: {
		attributes: {
				num_posts: 5,
				num_terms: 5,
		},
	},
	edit: ( props ) => {
		const {
			attributes: {
				show_excerpt,
				show_label,
				links,
				block_taxonomy,
				block_post_type,
				order,
				orderby,
			},
			className,
			setAttributes,
			isSelected,
			attributes
		} = props;

		function updateShowExcerpt( isChecked ) {
			setAttributes( { show_excerpt: isChecked } );
		}

		function updateShowLabel( isChecked ) {
			setAttributes( { show_label: isChecked } );
		}

		function updateLinks( isChecked ) {
			setAttributes( { links: isChecked } );
		}

		const sitemapGroupGeneralSettings = applyFilters( 'sitemap-group-general-settings', '', props );
		const sitemapGroupGeneralStyles = applyFilters( 'sitemap-group-general-styles', '', props );
		const sitemapGroupFeaturedImage = applyFilters( 'sitemap-group-featured-image', '', props );

		return [
			<InspectorControls key="simple-sitemap-group-block-controls">
				<PanelBody title={ __( 'General Settings', 'simple-sitemap' ) }>
					<PanelRow className="simple-sitemap">
						<SelectCptTaxonomy
							setAttributes={ setAttributes }
							multi={ false }
							block_post_type={ block_post_type }
							block_taxonomy={ block_taxonomy }
						/>
					</PanelRow>
					<PanelRow className="simple-sitemap order-label">
						<h3 style={ { marginBottom: '-12px' } }>
							Post ordering
						</h3>
					</PanelRow>
					<PanelRow className="simple-sitemap order">
						<SelectControl
							label="Orderby"
							value={orderby}
							options={[
								{ label: 'Title', value: 'title' },
								{ label: 'Date', value: 'date' },
								{ label: 'ID', value: 'ID' },
								{ label: 'Author', value: 'author' },
								{ label: 'Name', value: 'name' },
								{ label: 'Modified', value: 'modified' }
							]}
							onChange={ ( value ) => {
								setAttributes( { orderby: value } );
							} }
						/>
						<SelectControl
							label="Order"
							value={ order }
							options={ [
								{ label: 'Ascending', value: 'asc' },
								{ label: 'Descending', value: 'desc' }
							] }
							onChange={ ( value ) => {
								setAttributes( { order: value } );
							} }
						/>
					</PanelRow>
					<PanelRow className="simple-sitemap general-chk">
						<SitemapCheckboxControl
							value={ show_label }
							label="Display post type label"
							updateCheckbox={ updateShowLabel }
						/>
					</PanelRow>
					<PanelRow className="simple-sitemap general-chk">
						<SitemapCheckboxControl
							value={ show_excerpt }
							label="Display post excerpt"
							updateCheckbox={ updateShowExcerpt }
						/>
					</PanelRow>
					<PanelRow className="simple-sitemap general-chk">
						<SitemapCheckboxControl
							value={ links }
							label="Display sitemap links"
							updateCheckbox={ updateLinks }
						/>
					</PanelRow>
					{ sitemapGroupGeneralSettings }
				</PanelBody>
				{ sitemapGroupGeneralStyles }
				{ sitemapGroupFeaturedImage }
			</InspectorControls>,
			<ServerSideRenderX
			key="simple-sitemap-server-side-render-component"
			block="wpgoplugins/simple-sitemap-group-block"
			attributes={attributes}
		/>,
			// <ServerSideRender
			// 	key="simple-sitemap-group-server-side-render-component"
			// 	block="wpgoplugins/simple-sitemap-group-block"
			// 	attributes={ attributes }
			// />,
		];
	},
	save() {
		return null;
	},
} );
