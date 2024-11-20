import $ from 'jquery';

class MyNotes {
  constructor() {
    this.events();
  }

  events() {
    const notesContainer = $('#my-notes');
    notesContainer.on('click', '.delete-note', this.deleteNote.bind(this));
    notesContainer.on('click', '.edit-note', this.editNote.bind(this));
    notesContainer.on('click', '.update-note', this.updateNote.bind(this));
    $('.submit-note').on('click', this.createNote.bind(this));
  }

  editNote(e) {
    console.log('here');
    const currentNoteContainer = $(e.target).closest('.note-container');

    if (currentNoteContainer.data('state') === 'editable') {
      this.makeNoteReadOnly(currentNoteContainer);
    } else {
      this.makeNoteEditable(currentNoteContainer);
    }
  }

  makeNoteEditable(currentNoteContainer) {
    currentNoteContainer.data('state', 'editable');
    currentNoteContainer
      .find('.edit-note')
      .html(`<i class="fa fa-times" aria-hidden="true"></i> Cancel`);

    currentNoteContainer
      .find('.note-title-field, .note-body-field')
      .removeAttr('readonly')
      .addClass('note-active-field');

    currentNoteContainer.find('.update-note').addClass('update-note--visible');
  }

  makeNoteReadOnly(currentNoteContainer) {
    currentNoteContainer.data('state', 'cancel');

    currentNoteContainer
      .find('.edit-note')
      .html(`<i class="fa fa-pencil" aria-hidden="true"></i> Edit`);

    currentNoteContainer
      .find('.note-title-field, .note-body-field')
      .attr('readonly', 'readonly')
      .removeClass('note-active-field');

    currentNoteContainer
      .find('.update-note')
      .removeClass('update-note--visible');
  }

  createNote(e) {
    const noteTitle = $('.new-note-title');
    const noteBody = $('.new-note-body');
    const newNoteData = {
      title: noteTitle.val(),
      content: noteBody.val(),
      status: 'private', // fixes the default status of 'draft' when a note is created via REST
    };

    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
      },
      url: `${universityData.root_url}/wp-json/wp/v2/note/`,
      type: 'POST',
      data: newNoteData,
      success: (response) => {
        console.log('Created successfully!');
        console.log(response);
        noteTitle.val('');
        noteBody.val('');
        $(`
          <li class="note-container" data-note-id="${response.id}">
            <input readonly class="note-title-field" value="${response.title.raw}">
            <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
            <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
            <textarea readonly class="note-body-field">${response.content.raw}</textarea>
            <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
          </li>
          `)
          .prependTo('#my-notes')
          .hide()
          .slideDown();
      },
      error: (error) => {
        if (error.responseText === 'You have reached your note limit.') {
          $('.note-limit-message').addClass('active');
        }
        console.log('error');
        console.log(error);
      },
    });
  }

  updateNote(e) {
    const currentNoteContainer = $(e.target).closest('.note-container');
    const updatedData = {
      title: currentNoteContainer.find('.note-title-field').val(),
      content: currentNoteContainer.find('.note-body-field').val(),
    };

    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
      },
      url: `${
        universityData.root_url
      }/wp-json/wp/v2/note/${currentNoteContainer.data('note-id')}`,
      type: 'POST',
      data: updatedData,
      success: (response) => {
        console.log('Updated successfully');
        console.log(response);
        this.makeNoteReadOnly(currentNoteContainer);
      },
      error: (error) => {
        console.log('error');
        console.log(error);
      },
    });
  }

  deleteNote(e) {
    const currentNoteContainer = $(e.target).closest('.note-container');

    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
      },
      url: `${
        universityData.root_url
      }/wp-json/wp/v2/note/${currentNoteContainer.data('note-id')}`,
      type: 'DELETE',
      success: (response) => {
        console.log('Deleted successfully');
        console.log(response);
        currentNoteContainer.slideUp();
        if (response.user_note_count < 5) {
          $('.note-limit-message').removeClass('active');
        }
      },
      error: (error) => {
        console.log('error');
        console.log(error);
      },
    });
  }
}

export default MyNotes;
