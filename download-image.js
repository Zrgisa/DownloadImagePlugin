const el = wp.element.createElement;
const { __ } = wp.i18n;

const supportedGalleries = {
	'coblocks/gallery-masonry': 1,
	'core/gallery': 1,
};

const addDownloadButtonAttributes = ( settings, name ) => {
	if ( !supportedGalleries[name] ) {
		return settings;
	}

	settings.attributes = lodash.assign( settings.attributes, {
		downloadEnabled: {
			type: 'boolean',
			default: false,
		},
		downloadAllEnabled: {
			type: 'boolean',
			default: false,
		},
	} );

	return settings;
};

const downloadControl = wp.compose.createHigherOrderComponent( function( BlockEdit ) {
	return function( props ) {
		if ( !supportedGalleries[props.name] ) {
			return el(
				BlockEdit,
				props
			);
		}

		const { downloadEnabled, downloadAllEnabled } = props.attributes;

		return el(
			wp.element.Fragment,
			{},
			el(
				BlockEdit,
				props
			),
			el(
				wp.blockEditor.InspectorControls,
				{},
				el(
					wp.components.PanelBody,
					{
						title: 'Download Buttons',
						initialOpen: false
					},
					el(
						wp.components.ToggleControl,
						{
							label: 'Download Single Image',
							checked: downloadEnabled,
							onChange: function () {
								props.setAttributes( {
									downloadEnabled: !downloadEnabled,
								} );
							}
						}
					),
					el(
						wp.components.ToggleControl,
						{
							label: 'Download All',
							checked: downloadAllEnabled,
							onChange: function () {
								props.setAttributes( {
									downloadEnabled: !downloadAllEnabled,
								} );
							}
						}
					)
				)
			)
		);
	};
}, 'withDownloadControls' );

const addDownloadButton = ( element, blockType, attributes ) => {
	if ( !supportedGalleries[blockType.name] || !attributes.downloadEnabled ) {
		return element;
	}

	let listElement = element;
	let tree = [];
	while (listElement != null && listElement.type !== 'ul') {
		tree.push(listElement);
		listElement = React.Children.toArray(listElement.props.children)[0];
	}

	if (listElement === null) {
		return element;
	}

	const elements = React.Children.toArray(listElement.props.children);
	let newElements = [];
	const sizeRegex = /-\d+x\d+(\.[0-9a-zA-Z]*)?$/gm;

	for (let i = 0; i < elements.length; ++i) {
		let children = React.Children.toArray(elements[i].props.children);
		let url = '';

		for (let j = 0; j < children.length; ++j) {
			if (children[j].type !== 'figure') {
				continue;
			}

			const figChildren = React.Children.toArray(children[j].props.children);
			for (let k = 0; k < figChildren.length; ++k) {
				if (figChildren[k].type !== 'img') {
					continue;
				}

				url = figChildren[k].props.src;
				url = url.replace(sizeRegex, '$1');
			}
		}

		children.push(
			el(
				'div',
				{
					className: 'download-image-btn',
				},
				el(
					'a',
					{
						href: url,
						rel: 'noopener noreferrer',
						download: ''
					},
					'Download →'
				)
			)
		);

		newElements.push(React.cloneElement(
			elements[i],
			{},
			...children
		));
	}

	let lastElement = React.cloneElement(listElement, {}, ...newElements);
	let k = tree.length - 1;

	while (k >= 0) {
		lastElement = React.cloneElement(tree[k], {}, lastElement);
		--k;
	}

	return lastElement;
};

wp.hooks.addFilter( 'editor.BlockEdit', 'download-image/download-controls', downloadControl );
wp.hooks.addFilter( 'blocks.registerBlockType', 'download-image/attribute/download', addDownloadButtonAttributes );
wp.hooks.addFilter( 'blocks.getSaveElement', 'download-image/add-download-button', addDownloadButton );
