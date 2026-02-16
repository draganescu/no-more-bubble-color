export const createRoomEventSource = (roomHash: string): EventSource => {
  const topic = encodeURIComponent(`room:${roomHash}`);
  const url = `/.well-known/mercure?topic=${topic}`;
  return new EventSource(url);
};
