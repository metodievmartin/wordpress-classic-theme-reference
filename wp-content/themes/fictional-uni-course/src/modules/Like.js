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

    if ($currentLikeBox.attr('data-exists') === 'yes') {
      this.deleteLike($currentLikeBox);
    } else {
      this.createLike($currentLikeBox);
    }
  };

  createLike = ($currentLikeBox) => {
    console.log('create like');
    const professorID = $currentLikeBox.data('professor-id');

    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
      },
      type: 'POST',
      url: `${universityData.root_url}/wp-json/university/v1/manage-like`,
      data: {
        professor_id: professorID,
      },
      success: (response) => {
        console.log('response: ', response);

        if (!response || !response.success) {
          return;
        }

        $currentLikeBox.attr('data-exists', 'yes');
        $currentLikeBox.attr('data-like-id', response?.like_id);

        let likeCount = parseInt(
          $currentLikeBox.find('.like-count').html(),
          10
        );

        $currentLikeBox.find('.like-count').html(likeCount + 1);
      },
      error: (error) => {
        console.log('create like error: ', error);
      },
    });
  };

  deleteLike = ($currentLikeBox) => {
    console.log('delete like');
    const likeID = $currentLikeBox.data('like-id');

    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
      },
      type: 'DELETE',
      url: `${universityData.root_url}/wp-json/university/v1/manage-like`,
      data: {
        like_id: likeID,
      },
      success: (response) => {
        console.log('response: ', response);

        if (!response || !response.success) {
          return;
        }

        $currentLikeBox.attr('data-exists', 'no');
        $currentLikeBox.attr('data-like-id', '');

        let likeCount = parseInt(
          $currentLikeBox.find('.like-count').html(),
          10
        );

        $currentLikeBox.find('.like-count').html(likeCount - 1);
      },
      error: (error) => {
        console.log('create like error: ', error);
      },
    });
  };
}

export default Like;
