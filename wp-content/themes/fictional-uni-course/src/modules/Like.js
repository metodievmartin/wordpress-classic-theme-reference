import $ from 'jquery';

class Like {
  constructor() {
    this.events();
  }

  events = () => {
    $('.like-box').on('click', this.ourClickDispatcher);
  };

  ourClickDispatcher = (e) => {
    const $currentLikeBox = $(e.target).closest('.like-box');

    if ($currentLikeBox.data('exists') === 'yes') {
      this.deleteLike();
    } else {
      this.createLike();
    }
  };

  createLike = () => {
    console.log('create like');
  };

  deleteLike = () => {
    console.log('delete like');
  };
}

export default Like;
