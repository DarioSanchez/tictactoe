new Vue({
    el: "#game-view",
     data: function () {
	    return {
	     	inProgress : true,
        	winner : null,
        	currentTurn : 'O',
        	moveMade : 0,
        	squares : new Array(9).fill().map( s => new Square())
	    }
  	},
    methods :{
    	refresh(){
			location.reload();
			axios.get('app/http/controller/session_destroy.php')
				.then(response => {
					this.$toastr.success('Volver a jugar');

				}, response => {
					// error callback
				});
		},
	     makeMovie(i) {
	    	let params = [];
			if(this.inProgress && !this.squares[i].value){
				this.squares[i].value =  this.currentTurn;
				this.moveMade++;
				this.checkForWinner(i+1,this.currentTurn);
				this.currentTurn = (this.currentTurn === 'O' ) ? 'X' : 'O';
			}
		},
		checkForWinner(key,value) {
			axios.get('app/http/controller/tictactoeController.php',{
				params : {
					key : key,
					value : value
				}
			})
			.then(response => {
				if(response.data.winner) {
					this.inProgress = false;
					this.$toastr.success(response.data.result);
				}
			}, response => {
				// error callback
			});
			}
		},
		mounted () {


  		}
});
