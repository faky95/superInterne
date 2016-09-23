var TodoAfterAjaxEvent = function() {
	__constructor : function(){
	}, afterSuccesSubmitModal: function() {
		
	}
};

var todoAfterAjaxThread = new TodoAfterAjaxEvent();

function afterSuccesRequest(response) {
	todoAfterAjaxThread.afterSuccesSubmitModal(response);
}
