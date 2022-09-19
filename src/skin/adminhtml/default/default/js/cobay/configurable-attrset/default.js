function showCompare(url) {
	winCompare = new Window({
		className:'magento',
		title:'Quick AttributeSet for Configurable Product',
		url:url,
		width:820,
		height:500,
		minimizable:false,
		maximizable:false,
		showEffectOptions:{duration:0.4},
		hideEffectOptions:{duration:0.4}
	});
	winCompare.setZIndex(100);
	winCompare.showCenter(true);
}
