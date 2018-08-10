import child_process, { exec } from 'child_process';
import path from 'path'
import Logger, {infoStream} from '../lib/logger'
import * as globals from '../lib/globals'
import net from 'net'
import localStorage from '../lib/localStorage'
import isRunning from './helpers/serverOnline'
// Command Name *required
export const command = "start"

// Command Version
export const version = "0.0.1"

// Command Alias
export const alias = ""

// Command Description *required
export const description = "starts the server"

// Command Action *required
export const action = ()=>{
  if(isRunning()){
    console.log('Server is already online')
    return;
  }
  console.group('Starting Server')
  Logger.clear()
  localStorage.clear()

  var childFilePath = path.resolve(globals.InstallationDir()+'/dsm/serverWrapper.js');

  var options = {
    detached: true,
    stdio: ['ignore', infoStream, infoStream],
    execPath: childFilePath
  };
  const steamCmd = child_process.spawn('node',[childFilePath],options)
  steamCmd.unref();




}