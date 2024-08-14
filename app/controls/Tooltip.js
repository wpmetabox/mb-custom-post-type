const { Tooltip: T, Dashicon } = wp.components;

const Tooltip = ( { content } ) => (
  <T text={ content }>
    <span className="mb-cpt-tooltip-icon" tabIndex={ -1 }><Dashicon icon="editor-help" /></span>
  </T>
);
export default Tooltip;