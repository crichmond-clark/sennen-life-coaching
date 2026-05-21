( function( wp ) {
	'use strict';

	if ( ! wp || ! wp.blocks || ! wp.element ) {
		return;
	}

	var el = wp.element.createElement;
	var __ = wp.i18n ? wp.i18n.__ : function( text ) { return text; };
	var registerBlockType = wp.blocks.registerBlockType;
	var getBlockType = wp.blocks.getBlockType;
	var ServerSideRender = wp.serverSideRender;
	var InspectorControls = wp.blockEditor && wp.blockEditor.InspectorControls;
	var PanelBody = wp.components && wp.components.PanelBody;
	var RangeControl = wp.components && wp.components.RangeControl;
	var ToggleControl = wp.components && wp.components.ToggleControl;
	var TextControl = wp.components && wp.components.TextControl;
	var SelectControl = wp.components && wp.components.SelectControl;

	function registerSennenBlock( name, settings ) {
		if ( getBlockType( name ) ) {
			return;
		}

		registerBlockType( name, settings );
	}

	function preview( blockName, attributes ) {
		if ( ServerSideRender ) {
			return el( ServerSideRender, {
				block: blockName,
				attributes: attributes,
				skipBlockSupportAttributes: true
			} );
		}

		return el(
			'div',
			{ className: 'sennen-editor-placeholder' },
			__( 'Preview this block on the front end.', 'sennen-core' )
		);
	}

	function controls( fields ) {
		if ( ! InspectorControls || ! PanelBody || ! fields.length ) {
			return null;
		}

		return el(
			InspectorControls,
			null,
			el(
				PanelBody,
				{ title: __( 'Sennen settings', 'sennen-core' ), initialOpen: true },
				fields
			)
		);
	}

	registerSennenBlock( 'sennen/service-list', {
		apiVersion: 3,
		title: __( 'Service List', 'sennen-core' ),
		icon: 'heart',
		category: 'sennen-sections',
		description: __( 'Displays a grid of service cards from the Services content type.', 'sennen-core' ),
		attributes: {
			columns: { type: 'number', default: 3 },
			count: { type: 'number', default: 3 },
			orderBy: { type: 'string', default: 'sort_order' },
			showFeatures: { type: 'boolean', default: true },
			buttonText: { type: 'string', default: 'Inquire Now' },
			buttonUrl: { type: 'string', default: '/booking' }
		},
		supports: {
			align: [ 'wide', 'full' ],
			anchor: true,
			color: { background: true },
			spacing: { margin: true, padding: true }
		},
		edit: function( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			return [
				controls( [
					RangeControl && el( RangeControl, {
						label: __( 'Columns', 'sennen-core' ),
						value: attributes.columns,
						onChange: function( value ) { setAttributes( { columns: value } ); },
						min: 1,
						max: 4
					} ),
					RangeControl && el( RangeControl, {
						label: __( 'Number of services', 'sennen-core' ),
						value: attributes.count,
						onChange: function( value ) { setAttributes( { count: value } ); },
						min: 1,
						max: 12
					} ),
					ToggleControl && el( ToggleControl, {
						label: __( 'Show features', 'sennen-core' ),
						checked: attributes.showFeatures,
						onChange: function( value ) { setAttributes( { showFeatures: value } ); }
					} ),
					TextControl && el( TextControl, {
						label: __( 'Button text', 'sennen-core' ),
						value: attributes.buttonText,
						onChange: function( value ) { setAttributes( { buttonText: value } ); }
					} ),
					TextControl && el( TextControl, {
						label: __( 'Button URL', 'sennen-core' ),
						value: attributes.buttonUrl,
						onChange: function( value ) { setAttributes( { buttonUrl: value } ); }
					} )
				].filter( Boolean ) ),
				preview( 'sennen/service-list', attributes )
			];
		},
		save: function() {
			return null;
		}
	} );

	registerSennenBlock( 'sennen/testimonial-list', {
		apiVersion: 3,
		title: __( 'Testimonial List', 'sennen-core' ),
		icon: 'format-quote',
		category: 'sennen-sections',
		description: __( 'Displays a grid of testimonial cards.', 'sennen-core' ),
		attributes: {
			columns: { type: 'number', default: 3 },
			count: { type: 'number', default: 3 },
			showAvatar: { type: 'boolean', default: true },
			source: { type: 'string', default: 'latest' }
		},
		supports: {
			align: [ 'wide', 'full' ],
			anchor: true,
			color: { background: true },
			spacing: { margin: true, padding: true }
		},
		edit: function( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			return [
				controls( [
					RangeControl && el( RangeControl, {
						label: __( 'Columns', 'sennen-core' ),
						value: attributes.columns,
						onChange: function( value ) { setAttributes( { columns: value } ); },
						min: 1,
						max: 4
					} ),
					RangeControl && el( RangeControl, {
						label: __( 'Number of testimonials', 'sennen-core' ),
						value: attributes.count,
						onChange: function( value ) { setAttributes( { count: value } ); },
						min: 1,
						max: 12
					} ),
					ToggleControl && el( ToggleControl, {
						label: __( 'Show avatar', 'sennen-core' ),
						checked: attributes.showAvatar,
						onChange: function( value ) { setAttributes( { showAvatar: value } ); }
					} )
				].filter( Boolean ) ),
				preview( 'sennen/testimonial-list', attributes )
			];
		},
		save: function() {
			return null;
		}
	} );

	registerSennenBlock( 'sennen/botanical-divider', {
		apiVersion: 3,
		title: __( 'Botanical Divider', 'sennen-core' ),
		icon: 'palmtree',
		category: 'sennen-sections',
		description: __( 'A decorative botanical SVG divider to separate page sections.', 'sennen-core' ),
		attributes: {
			variant: { type: 'string', default: 'up' },
			opacity: { type: 'number', default: 30 }
		},
		supports: {
			anchor: true,
			spacing: { margin: true, padding: true }
		},
		edit: function( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			return [
				controls( [
					SelectControl && el( SelectControl, {
						label: __( 'Variant', 'sennen-core' ),
						value: attributes.variant,
						options: [
							{ label: __( 'Up', 'sennen-core' ), value: 'up' },
							{ label: __( 'Down', 'sennen-core' ), value: 'down' }
						],
						onChange: function( value ) { setAttributes( { variant: value } ); }
					} ),
					RangeControl && el( RangeControl, {
						label: __( 'Opacity', 'sennen-core' ),
						value: attributes.opacity,
						onChange: function( value ) { setAttributes( { opacity: value } ); },
						min: 0,
						max: 100
					} )
				].filter( Boolean ) ),
				preview( 'sennen/botanical-divider', attributes )
			];
		},
		save: function() {
			return null;
		}
	} );

	registerSennenBlock( 'sennen/booking-embed', {
		apiVersion: 3,
		title: __( 'Booking Embed', 'sennen-core' ),
		icon: 'calendar-alt',
		category: 'sennen-sections',
		description: __( 'Embeds a booking widget using the URL from Sennen Settings.', 'sennen-core' ),
		attributes: {
			useGlobalUrl: { type: 'boolean', default: true },
			bookingUrl: { type: 'string', default: '' },
			minHeight: { type: 'number', default: 700 }
		},
		supports: {
			align: [ 'wide', 'full' ],
			anchor: true,
			spacing: { margin: true, padding: true }
		},
		edit: function( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			return [
				controls( [
					ToggleControl && el( ToggleControl, {
						label: __( 'Use global booking URL', 'sennen-core' ),
						checked: attributes.useGlobalUrl,
						onChange: function( value ) { setAttributes( { useGlobalUrl: value } ); }
					} ),
					! attributes.useGlobalUrl && TextControl && el( TextControl, {
						label: __( 'Booking URL', 'sennen-core' ),
						value: attributes.bookingUrl,
						onChange: function( value ) { setAttributes( { bookingUrl: value } ); }
					} ),
					RangeControl && el( RangeControl, {
						label: __( 'Minimum height', 'sennen-core' ),
						value: attributes.minHeight,
						onChange: function( value ) { setAttributes( { minHeight: value } ); },
						min: 300,
						max: 1000
					} )
				].filter( Boolean ) ),
				preview( 'sennen/booking-embed', attributes )
			];
		},
		save: function() {
			return null;
		}
	} );

	registerSennenBlock( 'sennen/contact-form', {
		apiVersion: 3,
		title: __( 'Contact Form', 'sennen-core' ),
		icon: 'email-alt',
		category: 'sennen-sections',
		description: __( 'A contact form that sends submissions to the configured email.', 'sennen-core' ),
		attributes: {
			buttonText: { type: 'string', default: 'Send Message' },
			successMessage: { type: 'string', default: 'Your message has been sent. I look forward to connecting with you soon.' },
			inquiryTypes: { type: 'array', default: [] }
		},
		supports: {
			align: [ 'wide' ],
			anchor: true,
			spacing: { margin: true, padding: true }
		},
		edit: function( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			return [
				controls( [
					TextControl && el( TextControl, {
						label: __( 'Button text', 'sennen-core' ),
						value: attributes.buttonText,
						onChange: function( value ) { setAttributes( { buttonText: value } ); }
					} ),
					TextControl && el( TextControl, {
						label: __( 'Success message', 'sennen-core' ),
						value: attributes.successMessage,
						onChange: function( value ) { setAttributes( { successMessage: value } ); }
					} )
				].filter( Boolean ) ),
				preview( 'sennen/contact-form', attributes )
			];
		},
		save: function() {
			return null;
		}
	} );
} )( window.wp );
