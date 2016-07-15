import React, { Children, Component, PropTypes } from 'react';
import { render } from 'react-dom';

export default class Portal extends Component {
  constructor () {
    super();
    this.portalElement = null;
  }
  componentDidMount () {
    const p = document.createElement('div');
    document.body.appendChild(p);
    this.portalElement = p;
    this.componentDidUpdate();
  }
  componentDidUpdate () {
    render(
      this.props.children || <div />,
      this.portalElement
    );
  }
  componentWillUnmount () {
    document.body.removeChild(this.portalElement);
  }
  render () {
    return null;
  }
}

Portal.propTypes = {
  children: PropTypes.element,
};
