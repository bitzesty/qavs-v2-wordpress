//  Import core block libraries
//const { __ } = wp.i18n;
//const { InspectorControls } = wp.blockEditor;
const {
	// PanelBody,
	PanelRow,
	// ServerSideRender,
	// TextControl,
	// RadioControl,
	SelectControl,
	Button,
} = wp.components;
//const { registerBlockType } = wp.blocks;
const { Fragment, Component } = wp.element;
const { applyFilters, addAction, addFilter } = wp.hooks;
export class SelectCptTaxonomy extends Component {
	constructor( props ) {
		super(); // or super(props); ??

		this.state = {
			types: [],
			taxonomies: [],
			taxonomy_select_disabled_status: true,
			taxonomy_select_disabled_help: '',
			wrapperClass: '',
		};
		this.props = props;
		this.updatePostTypeValues = this.updatePostTypeValues.bind( this );
	}

	componentDidUpdate() {
		//console.log("UPDATE");
	}

	// get post types to populate select box
	componentDidMount() {

		//console.log("MOUNT");

		// Render drop downs.

		const fetchCPTs = wp.hooks.applyFilters( 'fetch-sitemap-cpts', null );
		if ( typeof fetchCPTs === 'function' ) {
			fetchCPTs( this.props ).then( ( post_types ) => {
				this.setState( {
					types: post_types,
				} );
			} );
		}

		this.fetchTaxonomies( null ); // null is important here
	}

	// set the taxonomy dropdown options
	fetchTaxonomies( newCPT = null ) {
		const { setAttributes, block_post_type, block_taxonomy } = this.props;

		// use the cpt from attribute (component did mount) or from new value when cpt drop down changed
		let current_post_type_arr;
		if ( newCPT ) {
			current_post_type_arr = newCPT;
		} else {
			current_post_type_arr = block_post_type;
		}
		
		const taxonomy_url = `simple-sitemap/v1/post-type-taxonomies/${ current_post_type_arr }`;

		wp.apiFetch( { path: taxonomy_url, method: 'GET' } ).then(
			( data ) => {
				let msg = '';
				let disabled_status = false;
				let wrapperClass = '';
				const taxonomies = [];
				let tax_flag = true;

				if ( data.length === 0 ) {
					msg = 'No taxonomies found for this post type';
					disabled_status = true;
					wrapperClass = 'disabled';
					setAttributes( { block_taxonomy: '' } );
				} else {
					const entries = Object.entries( data );
					for ( const [ key, value ] of entries ) {
						const tmp = {
							value: key,
							label: value,
						};
						taxonomies.push( tmp );

						// use attribute value?
						if ( tmp.value === block_taxonomy ) {
							tax_flag = false;
						}
					}
					// update attribute with first found taxonomy unless current taxonomy attr. is found in taxonomy array

					// only update tax attr. if current value not found in updated taxonomies array in which case just set to first taxonomy in array
					if ( tax_flag ) {
						setAttributes( {
							block_taxonomy: taxonomies[ 0 ].value,
						} );
					}
				}

				this.setState( {
					taxonomy_select_disabled_status: disabled_status,
					taxonomy_select_disabled_help: msg,
					taxonomies,
					wrapperClass,
				} );
				return data;
			},
			( err ) => {
				return err;
			}
		);
	}

	updatePostTypeValues( val ) {
		const { setAttributes } = this.props;
		setAttributes( { block_post_type: val } );
		this.fetchTaxonomies( val );
	}

	render() {
		const {
			setAttributes,
			block_post_type,
			block_taxonomy,
			multi = true,
			className,
		} = this.props;

		const sitemap_post_types = applyFilters(
			'sitemap-group-post-types-select',
			'',
			this.props,
			this.state,
			this.updatePostTypeValues
		);

		const sitemap_list_more_taxonomies = applyFilters(
			'sitemap-list-more-taxonomies',
			<PanelRow>
				<p
					style={ {
						marginTop: '-24px',
						fontSize: '13px',
						fontStyle: 'italic',
						marginLeft: '2px',
					} }
				>
					List&nbsp;
					<a
						href="https://wpgoplugins.com/plugins/simple-sitemap-pro/#taxonomies-for-any-post-type"
						target="_blank"
						rel="noreferrer"
					>
						taxonomies
					</a>
					&nbsp;for any post type
				</p>
			</PanelRow>
		);

		return (
			<div className={ `ss-taxonomy-select ${this.state.wrapperClass}` }>
				{sitemap_post_types}
				<SelectControl
					label="Select taxonomy"
					value={ block_taxonomy }
					options={ this.state.taxonomies }
					onChange={ ( val ) => {
						const { setAttributes } = this.props;
						setAttributes( { block_taxonomy: val } );
					} }
					disabled={ this.state.taxonomy_select_disabled_status }
				/>
			</div>
		);
	}
}
