import { default as sqlite3InitModule } from './sqlite3-bundler-friendly.js';
import './sqlite3-worker1-promiser.js';

const sqlite3Worker1Promiser = globalThis.sqlite3Worker1Promiser;

export default sqlite3InitModule;
export { sqlite3Worker1Promiser };
