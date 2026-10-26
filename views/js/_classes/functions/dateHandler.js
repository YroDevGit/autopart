export function convertTimezone(dateString, fromOffset = -7, toOffset = 8) {
    const [datePart, timePart] = dateString.split(' ');
    const [year, month, day] = datePart.split('-').map(Number);
    const [hour, minute, second] = timePart.split(':').map(Number);
    const utcMs = Date.UTC(year, month - 1, day, hour, minute, second);
    const sourceAsUtcMs = utcMs - fromOffset * 60 * 60 * 1000;

    const targetMs = sourceAsUtcMs + toOffset * 60 * 60 * 1000;
  
    const result = new Date(targetMs);

    const pad = (n) => String(n).padStart(2, '0');
    return (
      result.getUTCFullYear() + '-' +
      pad(result.getUTCMonth() + 1) + '-' +
      pad(result.getUTCDate()) + ' ' +
      pad(result.getUTCHours()) + ':' +
      pad(result.getUTCMinutes()) + ':' +
      pad(result.getUTCSeconds())
    );
  }