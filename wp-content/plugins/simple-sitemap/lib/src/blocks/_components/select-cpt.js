// This is used in the pro version to show all post types available.

import classnames from 'classnames';
import Select from 'react-select';

//  Import core block libraries
const { __ } = wp.i18n;
const { InspectorControls } = wp.blockEditor;
const {
	PanelBody,
	PanelRow,
	ServerSideRender,
	TextControl,
	RadioControl,
	SelectControl,
	Spinner
} = wp.components;
const { registerBlockType } = wp.blocks;
const { Component, Fragment } = wp.element;

export class SelectCPT extends Component {

	constructor(props) {
		super(); // or super(props); ??

		this.state = {
			loading: false,
			types: [],
		};
		this.props = props;
	}

	// get post types to populate select box
	componentDidMount() {

		this.setState({ loading: true });

		const url = 'simple-sitemap/v1/post-types';
		//const url = '/wp/v2/types';

		wp.apiFetch({ path: url, method: 'GET' }).then(
			(data) => {

				var post_types = [];

				const entries = Object.entries(data);
				for (const [key, value] of entries) {

					//console.log(key, value);

					const tmp = {
						value: key,
						label: value
					};

					post_types.push(tmp);
				}

				this.setState({
					types: post_types,
					loading: false
				});
				return data;
			},
			(err) => {
				return err;
			}
		);
	}

	render() {
		const { setAttributes, block_post_types, multi = true, className } = this.props;
		//console.log(JSON.stringify(this.props));

		// Not really needed as this displays a spinning icon EVERY time a sitemap block is clicked on which doesn't look great.
		// if ( this.state.loading ) {
		//   return (
		//     <p style={{'display': 'flex', 'align-items': 'center'}} className={className} >
		//       <Spinner />
		//       { __( 'Loading post types', 'simple-sitemap' ) }
		//     </p>
		//   );
		// }

		const selectStyles = {
			container: styles => ({
				...styles,
				marginBottom: "15px",
				'& div[class$="-Input"]': {
					'& input:focus': {
						boxShadow: 'none'
					}
				}
			})
		};
	
		return (
			<Select
				value={JSON.parse(block_post_types)}
				isMulti={multi}
				onChange={(val) => setAttributes({ block_post_types: JSON.stringify(val) })}
				options={this.state.types}
				className="react-select-container"
				classNamePrefix="react-select"
				styles={selectStyles}
			/>
		);
	}
}