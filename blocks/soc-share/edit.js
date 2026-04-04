import { __experimentalInputControl as InputControl, PanelBody, ColorPalette, ToggleControl }  from '@wordpress/components';
import { useBlockProps, useInnerBlocksProps, InspectorControls } from '@wordpress/block-editor';
const { __ } = wp.i18n;
import './editor.scss';

export default function edit( { attributes, setAttributes } ) {
	const { icoMarginRight, icoHeight, icoColor, icoColorHover, fbHidden, xtwHidden, lnHidden, wappHidden, telegHidden, emailHidden, linklHidden } = attributes;
	const colors = [
		{ name: 'black', color: '#000' },
		{ name: 'grey', color: '#999' },
	];
	const blockProps = useBlockProps();
    const innerBlocksProps = useInnerBlocksProps(blockProps);
	return (
		<>
			<InspectorControls>
				<PanelBody title={ 'Settings' }>
					<h3>Icon colour</h3>
					<ColorPalette
						label="Soc icon colour"
						colors={ colors }
						value={ icoColor }
						enableAlpha
						onChange={ ( colors ) => setAttributes( { icoColor: colors } ) }
					/>
					<h3>Icon hover colour</h3>
					<ColorPalette
						label="Soc icon hover colour"
						colors={ colors }
						value={ icoColorHover }
						enableAlpha
						onChange={ ( colors ) => setAttributes( { icoColorHover: colors } ) }
					/>
					<InputControl
						label="Icon margin"
						value={ icoMarginRight }
						type="number"
						min={ 0 }
						max={ 30 }
						isPressEnterToChange
						onChange={ ( newNum ) => setAttributes({ icoMarginRight: newNum }) }
					/>
					<InputControl
						label="Icon size"
						value={ icoHeight }
						type="number"
						min={ 10 }
						max={ 50 }
						isPressEnterToChange
						onChange={ ( newNum ) => setAttributes({ icoHeight: newNum }) }
					/>
				</PanelBody>
				<PanelBody title={'Hide Social Icons'} initialOpen={ true }>
					<div style={{ "display":"inline-flex","margin-bottom":"-15px","width":"100%" }}>
						<div style={{ "width":"30%", "display":"flex", "padding-top":"15px" }}><ToggleControl label="" help={ fbHidden ? 'Hidden' : 'Visible' } checked={ fbHidden } onChange={ () => setAttributes({ fbHidden: !fbHidden }) } /></div>
						<div style={{ "width":"70%", "display":"flex", "padding-top":"15px", "padding-left":"25px" }}>Facebook</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"-15px","width":"100%" }}>
						<div style={{ "width":"30%", "display":"flex", "padding-top":"25px" }}><ToggleControl label="" help={ xtwHidden ? 'Hidden' : 'Visible' } checked={ xtwHidden } onChange={ () => setAttributes({ xtwHidden: !xtwHidden }) } /></div>
						<div style={{ "width":"70%", "display":"flex", "padding-top":"25px", "padding-left":"25px" }}>Twitter X</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"-15px","width":"100%" }}>
						<div style={{ "width":"30%", "display":"flex", "padding-top":"25px" }}><ToggleControl label="" help={ lnHidden ? 'Hidden' : 'Visible' } checked={ lnHidden } onChange={ () => setAttributes({ lnHidden: !lnHidden }) } /></div>
						<div style={{ "width":"70%", "display":"flex", "padding-top":"25px", "padding-left":"25px" }}>Linkedin</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"-15px","width":"100%" }}>
						<div style={{ "width":"30%", "display":"flex", "padding-top":"25px" }}><ToggleControl label="" help={ wappHidden ? 'Hidden' : 'Visible' } checked={ wappHidden } onChange={ () => setAttributes({ wappHidden: !wappHidden }) } /></div>
						<div style={{ "width":"70%", "display":"flex", "padding-top":"25px", "padding-left":"25px" }}>WhatsApp</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"-15px","width":"100%" }}>
						<div style={{ "width":"30%", "display":"flex", "padding-top":"25px" }}><ToggleControl label="" help={ telegHidden ? 'Hidden' : 'Visible' } checked={ telegHidden } onChange={ () => setAttributes({ telegHidden: !telegHidden }) } /></div>
						<div style={{ "width":"70%", "display":"flex", "padding-top":"25px", "padding-left":"25px" }}>Telegram</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"-15px","width":"100%" }}>
						<div style={{ "width":"30%", "display":"flex", "padding-top":"25px" }}><ToggleControl label="" help={ emailHidden ? 'Hidden' : 'Visible' } checked={ emailHidden } onChange={ () => setAttributes({ emailHidden: !emailHidden }) } /></div>
						<div style={{ "width":"70%", "display":"flex", "padding-top":"25px", "padding-left":"25px" }}>Email</div>
					</div>
					<div style={{ "display":"inline-flex","margin-bottom":"-15px","width":"100%" }}>
						<div style={{ "width":"30%", "display":"flex", "padding-top":"25px" }}><ToggleControl label="" help={ linklHidden ? 'Hidden' : 'Visible' } checked={ linklHidden } onChange={ () => setAttributes({ linklHidden: !linklHidden }) } /></div>
						<div style={{ "width":"70%", "display":"flex", "padding-top":"25px", "padding-left":"25px" }}>Link</div>
					</div>
				</PanelBody>
			</InspectorControls>
			<div style={{ color: icoColor }} { ...innerBlocksProps }>
			{!fbHidden && (<svg class='fb_ico' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><path d='M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z' fill={icoColor}/></svg>)}
			{!xtwHidden && (<svg class='xtw_ico' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path d='M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z' fill={icoColor}/></svg>)}
			{!lnHidden && (<svg class='ln_ico' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512'><path d='M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z' fill={icoColor}/></svg>)}
			{!wappHidden && (<svg class='wapp_ico' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512'><path d='M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z'fill={icoColor}/></svg>)} 						{ !telegHidden && (<svg class='teleg_ico' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 496 512'><path d='M248,8C111.033,8,0,119.033,0,256S111.033,504,248,504,496,392.967,496,256,384.967,8,248,8ZM362.952,176.66c-3.732,39.215-19.881,134.378-28.1,178.3-3.476,18.584-10.322,24.816-16.948,25.425-14.4,1.326-25.338-9.517-39.287-18.661-21.827-14.308-34.158-23.215-55.346-37.177-24.485-16.135-8.612-25,5.342-39.5,3.652-3.793,67.107-61.51,68.335-66.746.153-.655.3-3.1-1.154-4.384s-3.59-.849-5.135-.5q-3.283.746-104.608,69.142-14.845,10.194-26.894,9.934c-8.855-.191-25.888-5.006-38.551-9.123-15.531-5.048-27.875-7.717-26.8-16.291q.84-6.7,18.45-13.7,108.446-47.248,144.628-62.3c68.872-28.647,83.183-33.623,92.511-33.789,2.052-.034,6.639.474,9.61,2.885a10.452,10.452,0,0,1,3.53,6.716A43.765,43.765,0,0,1,362.952,176.66Z' fill={icoColor}/></svg>)}
			{ !emailHidden && (<svg class="email_ico" xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path d='M64 112c-8.8 0-16 7.2-16 16v22.1L220.5 291.7c20.7 17 50.4 17 71.1 0L464 150.1V128c0-8.8-7.2-16-16-16H64zM48 212.2V384c0 8.8 7.2 16 16 16H448c8.8 0 16-7.2 16-16V212.2L322 328.8c-38.4 31.5-93.7 31.5-132 0L48 212.2zM0 128C0 92.7 28.7 64 64 64H448c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128z'fill={icoColor}/></svg>)}
			{ !linklHidden && (<svg class="link_ico"xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d='M384 336H192c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16l140.1 0L400 115.9V320c0 8.8-7.2 16-16 16zM192 384H384c35.3 0 64-28.7 64-64V115.9c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1H192c-35.3 0-64 28.7-64 64V320c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H256c35.3 0 64-28.7 64-64V416H272v32c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192c0-8.8 7.2-16 16-16H96V128H64z' fill={icoColor}/></svg>)}
			</div>
		</>
	)
}