// Register helpers for status and priority
Handlebars.registerHelper("statusClass", function (status) {
  switch (status) {
    case "Open":
      return "btn-primary";
    case "Close":
      return "btn-danger";
    case "Solved":
      return "btn-success";
    case "Pending":
      return "btn-warning";
    case "Unassigned":
      return "btn-secondary";
    default:
      return "";
  }
});

Handlebars.registerHelper("priorityClass", function (priority) {
  switch (priority) {
    case "Low":
      return "btn-success";
    case "Mid":
      return "btn-warning";
    case "High":
      return "btn-danger";
    default:
      return "";
  }
});
