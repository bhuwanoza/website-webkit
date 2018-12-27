const { __ } = wp.i18n;
const { InspectorControls, PanelColorSettings, RichText } = wp.editor;
const { registerBlockType } = wp.blocks;
const { RangeControl } = wp.components;
const { withState } = wp.compose;

import './style.scss';
import './editor.scss';

import { EmptyStar, HalfStar, FullStar } from './icons';

registerBlockType('gutenberg-blocks/star-rating', {
	title: __('Star Rating'),
	icon: HalfStar,
	category: 'gutenberg-blocks',

	attributes: {
		starCount: {
			type: 'number',
			default: 5
		},
		starSize: {
			type: 'number',
			default: 20
		},
		starColor: {
			type: 'string',
			default: '#ffff00'
		},
		selectedStars: {
			type: 'number',
			default: 0
		},
		reviewText: {
			type: 'array',
			source: 'children',
			selector: '.gutenberg-blocks-review-text'
		}
	},

	edit: withState({ editable: 'content' })(function(props) {
		const { editable, isSelected, setAttributes, setState } = props;

		const {
			starCount,
			starSize,
			starColor,
			selectedStars,
			reviewText
		} = props.attributes;

		const onSetActiveEditable = newEditable => () => {
			setState({ editable: newEditable });
		};

		return [
			isSelected && (
				<InspectorControls key="inspectors">
					<PanelColorSettings
						title={__('Star Color')}
						initialOpen={false}
						colorSettings={[
							{
								value: starColor,
								onChange: colorValue =>
									setAttributes({ starColor: colorValue }),
								label: __('')
							}
						]}
					/>
					<RangeControl
						label={__('Star size')}
						value={starSize}
						onChange={value => setAttributes({ starSize: value })}
						min={10}
						max={30}
						beforeIcon="editor-contract"
						afterIcon="editor-expand"
						allowReset
					/>
					<RangeControl
						label={__('Number of stars')}
						value={starCount}
						onChange={value =>
							setAttributes({
								starCount: value,
								selectedStars:
									value < selectedStars
										? value
										: selectedStars
							})
						}
						min={5}
						max={10}
						beforeIcon="star-empty"
						allowReset
					/>
					<RangeControl
						label={__('Number of selected stars')}
						value={selectedStars}
						onChange={value =>
							setAttributes({ selectedStars: value })
						}
						min={0}
						max={starCount}
						beforeIcon="star-filled"
						allowReset
					/>
				</InspectorControls>
			),
			<div className="gutenberg-blocks-star-rating">
				<div className="gutenberg-blocks-star-container">
					{[...Array(starCount)].map((e, i) => (
						<div key={i}>
							{i < selectedStars ? (
								<FullStar
									size={starSize}
									fillColor={starColor}
								/>
							) : (
								<EmptyStar size={starSize} />
							)}
						</div>
					))}
				</div>
				<div key={'editable'} className="gutenberg-blocks-review-text">
					<RichText
						tagName="div"
						placeholder={__('The text of the review goes here')}
						value={reviewText}
						onChange={text => setAttributes({ reviewText: text })}
						keepPlaceholderOnFocus={true}
						isSelected={isSelected && editable === 'review_text'}
						onFocus={onSetActiveEditable('review_text')}
					/>
				</div>
			</div>
		];
	}),

	save(props) {
		const {
			starCount,
			starSize,
			starColor,
			selectedStars,
			reviewText
		} = props.attributes;
		return (
			<div className="gutenberg-blocks-star-rating">
				<div className="gutenberg-blocks-star-container">
					{[...Array(starCount)].map((e, i) => (
						<div key={i}>
							{i < selectedStars ? (
								<FullStar
									size={starSize}
									fillColor={starColor}
								/>
							) : (
								<EmptyStar size={starSize} />
							)}
						</div>
					))}
				</div>
				<div className="gutenberg-blocks-review-text">{reviewText}</div>
			</div>
		);
	}
});
