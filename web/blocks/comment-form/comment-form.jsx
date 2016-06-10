import React from 'react';

export default class CommentForm extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isSending: false,
      value: '',
    };
  }

  componentDidMount() {
    if (this.props.parentId) {
      this.refs.body.focus();
    }
  }

  submit(e) {
    if (e) {
      e.preventDefault();
    }
    var xhr = new XMLHttpRequest();
    var method = 'POST';//isCreate ? 'POST' : 'PUT';
    var formData = new FormData(this.refs.form);
    formData.append('targetId', this.props.contentId);
    if (this.props.parentId) {
      formData.append('parentId', this.props.parentId);
    }
    formData.append('targetType', 2);
    xhr.open(method, '/api/comment/write');
    xhr.send(formData);
    this.setState({isSending: true});
    xhr.onload = () => {
      this.refs.form.body.value = '';
      const data = JSON.parse(xhr.responseText);
      this.setState({isSending: false});
      this.props.onNew(data.result, this.props.parentId);
    };
  }

  onChange(e) {
    this.setState({error: false});
    this.setState({value: e.target.value});
  }

  onKeyDown(e) {
    if ((e.keyCode===10 || e.keyCode===13) && e.ctrlKey) {
      e.currentTarget.blur();
      this.submit();
    }
  }

  render() {
    if (!this.props.user.id) {
      return <div className="comments-info">
        <span className="fa fa-exclamation-triangle"></span>
        Только зарегистрированные пользователи могут участвовать в обсуждениях.<br />
        Если вы хотите оставить комментарий, пожалуйста, <a href="#" onClick={Auth.open}>представьтесь</a> или <a href="/#2">зарегистрируйтесь</a>.
      </div>;
    }
    return <form className="form form-comment" ref="form" onSubmit={this.submit.bind(this)}>
      {this.props.parentId ?
        <fieldset>
          <legend>Ваш комментарий</legend>
        </fieldset>
      : null}
      <fieldset className={this.state.error ? 'error' : ''}>
        <textarea ref="body" onKeyDown={this.onKeyDown.bind(this)} onChange={this.onChange.bind(this)} rows="10" cols="30" name="body"></textarea>
        <span className="error-message"></span>
      </fieldset>
      <fieldset>
        <input type="submit" value="Отправить" disabled={!this.state.value || this.state.isSending} />
        {this.props.children}
      </fieldset>
    </form>;
  }
}
